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
            'title' => ['required', 'string', 'max:255', 'min:3'],
            'description' => ['nullable', 'string'],
            'meeting_point' => ['required', 'string', 'max:255'],
            'period_start' => ['required', 'date', $this->validatePeriodStart()],
            'period_end' => ['required', 'date', $this->validatePeriodEnd()],
            'start_time' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'requires_ticket' => ['required', 'boolean'],
            'min_participants' => ['required', 'integer', 'min:1', 'max:100'],
            'max_participants' => ['required', 'integer', 'min:1', 'gte:min_participants', 'max:100'],
        ];
    }

    private function validatePeriodStart(): \Closure
    {
        return function ($attribute, $value, $fail) {
            $startDate = \Carbon\Carbon::parse($value);
            $today = \Carbon\Carbon::today();

            if ($startDate->gt($today->copy()->addYears(2))) {
                $fail('Period of availability must start no later than two years from today.');
            }

            if ($startDate->lt($today)) {
                $fail('Period of availability cannot begin on a past date.');
            }
        };
    }

    private function validatePeriodEnd(): \Closure
    {
        return function ($attribute, $value, $fail) {
            $start = $this->input('period_start');

            if ($start) {
                $startDate = \Carbon\Carbon::parse($start);
                $endDate = \Carbon\Carbon::parse($value);

                if ($endDate->gt($startDate->copy()->addYears(1))) {
                    $fail('Period of availability must not be longer than one year.');
                }

                if ($endDate->lt($startDate)) {
                    $fail('This date cannot be set before the start date.');
                }
            }
        };
    }

}
