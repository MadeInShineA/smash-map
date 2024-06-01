<?php

namespace App\Http\Controllers;

use App\Enums\ImageTypeEnum;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SettingsDistanceNotificationsRadiusUpdateRequest;
use App\Http\Requests\SettingsUpdateRequest;
use App\Http\Resources\Character\CharacterResource;
use App\Http\Resources\Game\GameResource;
use App\Http\Resources\Image\ImageResource;
use App\Http\Resources\Notification\NotificationResource;
use App\Http\Resources\User\LocalStorageUserResource;
use App\Http\Resources\User\ProfileUserResource;
use App\Http\Resources\User\SettingsUserResource;
use App\Models\Address;
use App\Models\Country;
use App\Models\Event;
use App\Models\Image;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Geometry\Factories\CircleFactory;
use Intervention\Image\ImageManager;

class UserController extends Controller
{
    public function register(RegisterRequest $request):JsonResponse
    {
        try{
            $country = Country::where('code',$request->input('countryCode'))->first();
            $address = Address::firstOrCreate([
                'latitude' =>$request->input('latitude'),
                'longitude' =>$request->input('longitude')],
                ['name'=>$request->input('addressName'),
                    'country_id' =>$country->id
                ]);
            $user = User::create([
                'uuid'          => Str::uuid()->toString(),
                'username'      => $request->input('username'),
                'email'         => $request->input('email'),
                'password'      => Hash::make($request->input('password')),
                'address_id'    => $address->id,
                'is_modder'      => $request->input('isModder'),
                'is_on_map'      => $request->input('isOnMap'),
            ]);

            $user->games()->attach($request->input('games'));
            $user->characters()->attach($request->input('characters'));

            $profile_picture = file_get_contents('https://ui-avatars.com/api/?name=' . $user->username . '&rounded=true&length=1&background=random');
            $user_directory_path = '/users-images/' . $user->uuid;
            Storage::put($user_directory_path . '/' . ImageTypeEnum::USER_PROFILE . '.png', $profile_picture);
            Image::Create(['parentable_type' =>User::class, 'parentable_id' =>$user->id, 'type' =>ImageTypeEnum::USER_PROFILE, 'extension' => 'png']);

            return $this->sendResponse(['user' => new LocalStorageUserResource($user->fresh()), 'token' => $user->createToken('API Token')->plainTextToken], 'You are registered and connected!');
        }catch (\Error $error){
            return $this->sendError('An error occurred while registering E 001', [$error], 500);
        }
    }
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            if (!Auth::attempt($request->json()->all())) {
                return $this->sendError('Unauthorized  E 002',
                    [
                        'login' => ['Username or password are incorrect']
                    ],
                    401);
            }
            return $this->sendResponse(['user' => new LocalStorageUserResource(auth::user()), 'token' => auth::user()->createToken('API Token')->plainTextToken], 'You are connected');
        }catch (\Error $error) {
            return $this->sendError($error, ['login' => ['An error occurred while logging in E 003']], 500);
        }
    }

    public function logout(Request $request):JsonResponse
    {
        try {
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();
            }
            return $this->sendResponse([], 'You are disconnected');
        } catch (\Error $error) {
            return $this->sendError('An error occurred while logging out E 007', [$error], 500);
        }
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        try{
            if ($user->id != $request->user('sanctum')->id) {
                return $this->sendError('You are not authorized to delete this account', [], 401);
            }
            $user->delete();
            // TODO Remove the users directory
            return $this->sendResponse([], 'Account deleted with success');
        }catch (\Error $error) {
            return $this->sendError('An error occurred while deleting the account E 014', [$error], 500);
        }


    }

    public function forgot_password(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );
            return $status === Password::RESET_LINK_SENT
                ? $this->sendResponse([], __($status))
                : $this->sendError( __($status),[], 500);
        } catch (\Error $error) {
            return $this->sendError('An error occurred while sending the email E 004', [$error], 500);
        }
    }

    public function reset_password(ResetPasswordRequest $request):JsonResponse
    {
        try{
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            return $status === Password::PASSWORD_RESET
                ? $this->sendResponse([], __($status))
                : $this->sendError(__($status), [], 500);
        }catch (\Error $error) {
            return $this->sendError('An error occurred while sending the email E 005', [$error], 500);
        }

    }

    public function check_authentication(Request $request, User $user): JsonResponse
    {
        if ($user->id != $request->user('sanctum')->id){
            return $this->sendError('You are not authorized to access this page', [], 401);
        }
        return $this->sendResponse([], 'You are authenticated');
    }

    public function get_profile(Request $request, User $user): JsonResponse
    {
        return $this->sendResponse(new ProfileUserResource($user), 'User retrieved with success');
    }

    public function update_profile(ProfileUpdateRequest $request, User $user): JsonResponse
    {
        try{
            $data = [];
            $profile_picture = $request->file('profilePicture');
            if($profile_picture){

                $image_directory_path = '/users-images/' . $user->uuid;


                $previous_image = $user->images()->where('type', ImageTypeEnum::USER_PROFILE)->first();

                $previous_image_path = base_path(). '/storage/app/public' . $image_directory_path . '/' . ImageTypeEnum::USER_PROFILE .'.' . $previous_image->extension;
                unlink($previous_image_path);

                $previous_image->delete();

                $image_extension = $profile_picture->extension();
                $image_file_url = $image_directory_path . '/' . ImageTypeEnum::USER_PROFILE . '.' . $image_extension;

                $image_manager = new ImageManager(new Driver());
                $image = $image_manager->read($profile_picture->path());

                $image->cover(640, 640, 'center');
                $image->save(base_path() . '/storage/app/public' . $image_file_url);

                Image::Create(['parentable_type' =>User::class, 'parentable_id' =>$user->id, 'type' =>ImageTypeEnum::USER_PROFILE, 'extension' => $image_extension]);



                $user->has_default_profile_picture = false;
                $user->refresh();
                $data['profilePicture'] = new ImageResource($user->getProfilePictureAttribute());
            }
            $user->update([
                'description'       => $request->input('description'),
                'discord'           => $request->input('discord'),
                'x'                 => $request->input('x'),
                'connect_code'      => $request->input('connectCode'),
            ]);

            return $this->sendResponse($data, 'Profile updated with success');

        }catch (\Error $error) {
            return $this->sendError('An error occurred while updating the profile E 018', [$error], 500);
        }
    }
}
