<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlaceRequest extends FormRequest
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
        // Get the place ID from the route parameters
        $placeId = $this->route('place')->place_id;

        return [
            // Unique rule needs to ignore the current place's name
            'name' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('places', 'name')->ignore($placeId, 'place_id')],
            'description' => ['nullable', 'string'],
            'location' => ['required', 'string', 'max:255'],
        ];
    }
}
