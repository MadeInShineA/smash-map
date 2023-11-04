<?php

namespace App\Http\Requests;

use App\Models\Character;
use App\Models\Game;
use App\Models\User;
use App\Rules\AtLeastOneCharacterPerGame;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RegisterRequest extends FormRequest
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
            'username'              => 'required|max:20|unique:' .User::class . ',username' ,
            'email'                 => 'required|email:rfc,dns|max:255|unique:' . User::class . ',email',
            'password'              => 'required',
            'passwordConfirmation'  => 'required|same:password',
            'games'                 => 'required|exists:' . Game::class . ',id',
            'characters'            => ['required', 'exists:' .Character::class . ',id', new AtLeastOneCharacterPerGame],
            'addressName'           => 'required',
            'latitude'              => ['required','regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude'             => ['required','regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'countryCode'           => 'required',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $addressNameError = $validator->errors()->get('addressName');
            $latitudeError = $validator->errors()->get('latitude');
            $longitudeError = $validator->errors()->get('longitude');
            $countryCodeError = $validator->errors()->get('countryCode');


            // Check if there's an error for 'latitude' or 'longitude'
            if (empty($addressNameError) && (!empty($latitudeError) || !empty($longitudeError) || !empty($countryCodeError))) {
                    $validator->errors()->add('addressName', 'Please use the Google Autocomplete');
                }
        });
    }
}
