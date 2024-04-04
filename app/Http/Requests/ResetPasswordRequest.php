<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'token'                 => 'required|string',
            'email'                 => 'required|email|exists:users,email',
            'password'              => 'required|string',
            'passwordConfirmation' => 'required|string|same:password'
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => 'There is no account with this email address.'
        ];
    }
}
