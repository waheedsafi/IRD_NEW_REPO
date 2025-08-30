<?php

namespace App\Http\Requests\app\schedule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class ScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'dinner_start' => 'nullable|string',
            'dinner_end' => 'nullable|string',
            'gap_between' => 'required|integer',
            'lunch_start' => 'nullable|string',
            'lunch_end' => 'nullable|string',
            'presentation_length' => 'required|integer',
            'presentation_count' => 'required|integer',
            'presentations_after_lunch' => 'nullable|integer',
            'presentations_before_lunch' => 'nullable|integer',
            'is_hour_24' => 'required|boolean',
            'scheduleItems' => 'required|array',
            'scheduleItems.*.projectId' => 'required|integer',
            'scheduleItems.*.slot.id' => 'nullable|integer',
            'scheduleItems.*.slot.presentation_start' => 'required|string',
            'scheduleItems.*.slot.presentation_end' => 'required|string',
            'scheduleItems.*.selected' => 'nullable|boolean',
        ];
    }
}
