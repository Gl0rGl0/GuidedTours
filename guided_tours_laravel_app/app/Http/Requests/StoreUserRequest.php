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
        // Admins should only be able to add 'configurator' or 'volunteer' roles
        $allowed_roles = ['configurator', 'volunteer'];

        return [
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(6)],
            'role' => ['required', \Illuminate\Validation\Rule::in($allowed_roles)],
        ];
    }
}
