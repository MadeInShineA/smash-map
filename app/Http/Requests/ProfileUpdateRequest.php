<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            // TODO Check the profilePicture extension and fix the error message when the file's size > upload_max_filesize inside php.ini
            'profilePicture'    => ['nullable', 'mimes:jpeg,png,jpg', 'max:1'],
            'description'       => ['nullable', 'string', 'max:255'],
            'discord'           => ['nullable', 'string', 'max:32'],
            'x'                 => ['nullable', 'string', 'max:15'],
            'connectCode'       => ['nullable', 'string', 'max:8', 'regex:/#\d+/'],
        ];
    }

}
