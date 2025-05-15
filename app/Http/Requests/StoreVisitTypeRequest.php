<?php

namespace App\Http\Requests;
use Carbon\Carbon;

class StoreVisitTypeRequest extends BaseFormRequest
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

        return [
            'place_id' => ['required', 'integer', 'exists:places,place_id'],
            'title' => ['required', 'string', 'max:255', 'min:3', 'unique:visit_types,title'],
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
