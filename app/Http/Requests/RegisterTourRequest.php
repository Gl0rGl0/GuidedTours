<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RegisterTourRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'num_participants' => ['required', 'integer', 'min:1'],
        ];
    }
    
    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'visit_id.required' => 'The visit ID is required.',
            'visit_id.exists' => 'The selected visit does not exist.',
            'num_participants.required' => 'The number of participants is required.',
            'num_participants.integer' => 'The number of participants must be a whole number.',
            'num_participants.min' => 'You must register at least one participant.',
        ];
    }
}
