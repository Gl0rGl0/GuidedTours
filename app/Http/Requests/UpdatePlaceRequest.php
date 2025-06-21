<?php

namespace App\Http\Requests;

class UpdatePlaceRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string,\Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the place ID from the route parameters
        $placeId = $this->route('place')->place_id;

        return [
            // Unique rule needs to ignore the current place's name
            //Rule::unique(...)->ignore(...) applica l’unicità escludendo il record che stai aggiornando, permettendoti di non cambiare il nome senza incorrere in errore di duplicato.
            'name' => ['required', 'string', 'min:3', 'max:255', \Illuminate\Validation\Rule::unique('places', 'name')->ignore($placeId, 'place_id')],
            'description' => ['nullable', 'string'],
            'location' => ['required', 'string', 'max:255'],
        ];
    }
}
