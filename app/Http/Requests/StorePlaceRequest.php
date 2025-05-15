<?php

namespace App\Http\Requests;

class StorePlaceRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string,\Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'min:3', 'max:255', 'unique:places,name'],
            'description' => ['nullable', 'string'],
            'location'    => ['required', 'string', 'max:255'],
        ];
    }
}