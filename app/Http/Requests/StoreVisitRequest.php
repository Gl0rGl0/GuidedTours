<?php

namespace App\Http\Requests;

class StoreVisitRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'visit_type_id' => ['required', 'integer'],
            'visit_date'    => ['required', 'date'],
            'assigned_volunteer_id' => ['required', 'integer']
        ];
    }
}