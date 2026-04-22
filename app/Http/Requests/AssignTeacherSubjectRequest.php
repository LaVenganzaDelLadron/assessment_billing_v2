<?php

namespace App\Http\Requests;

use Illuminate\Validation\Validator;

class AssignTeacherSubjectRequest extends CrudRequest
{
    public function rules(): array
    {
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'subject_id' => ['sometimes', 'required', 'exists:subjects,id'],
                'days' => ['sometimes', 'required', 'array', 'min:1'],
                'days.*' => ['required', 'string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
                'start_time' => ['sometimes', 'required', 'date_format:H:i'],
                'end_time' => ['sometimes', 'required', 'date_format:H:i'],
                'room' => ['nullable', 'string', 'max:255'],
                'status' => ['nullable', 'in:active,inactive'],
            ];
        }

        return [
            'subject_id' => ['required', 'exists:subjects,id'],
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['required', 'string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'room' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:active,inactive'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $startTime = $this->input('start_time');
                $endTime = $this->input('end_time');

                if (
                    is_string($startTime) &&
                    is_string($endTime) &&
                    strtotime($startTime) >= strtotime($endTime)
                ) {
                    $validator->errors()->add('end_time', 'The end time must be later than the start time.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'subject_id.required' => 'The subject field is required.',
            'subject_id.exists' => 'The selected subject does not exist.',
            'days.required' => 'Please select at least one day.',
            'days.array' => 'The days field must be a list of days.',
            'days.min' => 'Please select at least one day.',
            'days.*.in' => 'One of the selected days is invalid.',
            'start_time.required' => 'The start time field is required.',
            'start_time.date_format' => 'The start time must use the HH:MM format.',
            'end_time.required' => 'The end time field is required.',
            'end_time.date_format' => 'The end time must use the HH:MM format.',
            'room.max' => 'The room may not be greater than 255 characters.',
            'status.in' => 'The status must be either active or inactive.',
        ];
    }
}
