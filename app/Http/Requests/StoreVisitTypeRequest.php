<?php

namespace App\Http\Requests;

class StoreVisitTypeRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string,\Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'place_id' => ['required', 'integer', 'exists:places,place_id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'meeting_point' => ['required', 'string', 'max:255'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'requires_ticket' => ['required', 'boolean'],
            'min_participants' => ['required', 'integer', 'min:1'],
            'max_participants' => ['required', 'integer', 'min:1', 'gte:min_participants'],
        ];
    }
}
