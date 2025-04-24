<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by middleware
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Role assignment is now handled by Spatie after user creation,
        // so the 'role' validation is removed.
        // The logic for which roles an admin can assign should be handled
        // in the controller or a dedicated action class.

        return [
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(6)],
            // Removed 'role' validation
        ];
    }
}
