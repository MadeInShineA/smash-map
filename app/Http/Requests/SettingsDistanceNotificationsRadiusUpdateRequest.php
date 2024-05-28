<?php

namespace App\Http\Requests;

use App\Models\Character;
use App\Models\Game;
use App\Models\User;
use App\Rules\AtLeastOneCharacterPerGame;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SettingsDistanceNotificationsRadiusUpdateRequest extends FormRequest
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
            'distanceNotificationsRadius' => ['required', 'numeric', 'min:1', 'max:2000']
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
