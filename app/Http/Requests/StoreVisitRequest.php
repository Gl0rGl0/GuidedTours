<?php

namespace App\Http\Requests;

class StoreVisitRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string,\Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'visit_type_id' => ['required', 'integer'],
            'visit_date'    => ['required', 'date'],
            'assigned_volunteer_id' => ['required', 'integer']
        ];
    }
}