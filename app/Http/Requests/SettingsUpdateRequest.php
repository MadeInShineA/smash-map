<?php

namespace App\Http\Requests;

use App\Models\Character;
use App\Models\Game;
use App\Models\User;
use App\Rules\AtLeastOneCharacterPerGame;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SettingsUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'username'              => 'required|max:20|alpha_dash:ascii|'. Rule::unique('users', 'username')->ignore($this->user->id) ,
            'email'                 => 'required|email:rfc,dns|max:255|' . Rule::unique('users', 'email')->ignore($this->user->id),
            'password'              => 'nullable|string',
            'passwordConfirmation'  => 'nullable|required_with:password|string|same:password',
            'games'                 => 'required|exists:' . Game::class . ',id',
            'characters'            => ['required', 'exists:' .Character::class . ',id', new AtLeastOneCharacterPerGame],
            'address'               => 'required|array',
            'address.name'          => 'required',
            'address.latitude'      => ['required','regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'address.longitude'     => ['required','regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'address.countryCode'   => 'required',
            'isModder'              => 'required|boolean',
            'isOnMap'               => 'required|boolean',
            'notifications'         => 'required|array',
            'notifications.*'       => 'required|in:distanceNotifications,timeNotifications,attendeesNotifications',
            'distanceNotificationsRadius' => ['nullable','required_if:notifications.*,distanceNotifications', 'numeric', 'min:1', 'max:2000'],
            'attendeesNotificationsThresholds' => ['nullable','required_if:notifications.*,attendeesNotifications', 'array', 'min:1'],
            'attendeesNotificationsThresholds.*' => 'numeric|min:1|distinct',
            'timeNotificationsThresholds' => ['nullable','required_if:notifications.*,timeNotifications', 'array', 'min:1'],
            'timeNotificationsThresholds.*' => 'numeric|min:0|distinct',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $addressNameError = $validator->errors()->get('address.name');
            $latitudeError = $validator->errors()->get('address.latitude');
            $longitudeError = $validator->errors()->get('address.longitude');
            $countryCodeError = $validator->errors()->get('address.countryCode');


            // Check if there's an error for 'latitude' or 'longitude'
            if (empty($addressNameError) && (!empty($latitudeError) || !empty($longitudeError) || !empty($countryCodeError))) {
                $validator->errors()->add('address', 'Please use the Google Autocomplete');
            }

            $attendeesNotificationsThresholdsError = $validator->errors()->get('attendeesNotificationsThresholds.*');
            $timeNotificationsThresholdsError = $validator->errors()->get('timeNotificationsThresholds.*');

            if($attendeesNotificationsThresholdsError){
                $validator->errors()->add('attendeesNotificationsThresholds', 'Please provide valid numbers for thresholds');
            }
            if($timeNotificationsThresholdsError){
                $validator->errors()->add('timeNotificationsThresholds', 'Please provide valid numbers for thresholds');
            }
        });
    }
}
