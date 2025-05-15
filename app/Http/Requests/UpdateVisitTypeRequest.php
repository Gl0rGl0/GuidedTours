<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitTypeRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string,\Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxStart = Carbon::today()
            ->copy()
            ->addYears(config('app.max_year_tv'))
            ->toDateString();

        // se serve il periodo end relativo al start inviato:
        $periodStart = $this->input('period_start');
        $maxEnd = Carbon::parse($periodStart)
                ->copy()
                ->addYears(config('app.max_duration_tv'))
                ->toDateString();

        $visit_type_id = $this->route('visit_type')->visit_type_id;

        return [
            'place_id' => ['required', 'integer', 'exists:places,place_id'],
            'title' => ['required', 'string', 'min:3', 'max:255', \Illuminate\Validation\Rule::unique('visit_types', 'title')->ignore($visit_type_id, 'visit_type_id')],
            'description' => ['nullable', 'string'],
            'meeting_point' => ['required', 'string', 'max:255'],
            'period_start' => ['required', 'date', 'after_or_equal:today', "before_or_equal:{$maxStart}"],
            'period_end'   => ['required', 'date', 'after_or_equal:period_start', "before_or_equal:{$maxEnd}"],
            'start_time' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'requires_ticket' => ['required', 'boolean'],
            'min_participants' => ['required', 'integer', 'min:1', 'max:100'],
            'max_participants' => ['required', 'integer', 'min:1', 'gte:min_participants', 'max:100'],
        ];
    }
}
