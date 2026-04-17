<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AssessmentsRequest extends CrudRequest
{

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

        if($this->isMethod('put') || $this->isMethod('put')) {
            return [
                'student_id' => ['sometimes', 'required', 'exists:students,id'],
                'academic_term_id' => ['sometimes', 'required', 'exists:academic_terms,id'],
                'semester' => ['sometimes', 'required', 'string', 'max:20'],
                'school_year' => ['sometimes', 'required', 'string', 'max:9'],
                'total_units' => ['sometimes', 'required', 'numeric', 'min:0'],
                'tuition_fee' => ['sometimes', 'required', 'numeric', 'min:0'],
                'misc_fee' => ['sometimes', 'required', 'numeric', 'min:0'],
                'lab_fee' => ['sometimes', 'required', 'numeric', 'min:0'],
                'other_fees' => ['sometimes', 'required', 'numeric', 'min:0'],
                'total_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
                'discount' => ['sometimes', 'required', 'numeric', 'min:0'],
                'net_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
                'status' => ['sometimes', 'required', Rule::in(['draft', 'finalized'])],
            ];
        }
        return [];
    }
    public function messages(): array
    {
        return [
            'student_id.exists' => 'The selected student does not exist.',
            'academic_term_id.exists' => 'The selected academic term does not exist.',
            'semester.required' => 'The semester field is required.',
            'school_year.required' => 'The school year field is required.',
            'total_units.numeric' => 'The total units must be a number.',
            'total_units.min' => 'The total units must be at least 0.',
            'tuition_fee.numeric' => 'The tuition fee must be a number.',
            'tuition_fee.min' => 'The tuition fee must be at least 0.',
            'misc_fee.numeric' => 'The miscellaneous fee must be a number.',
            'misc_fee.min' => 'The miscellaneous fee must be at least 0.',
            'lab_fee.numeric' => 'The lab fee must be a number.',
            'lab_fee.min' => 'The lab fee must be at least 0.',
            'other_fees.numeric' => 'The other fees must be a number.',
            'other_fees.min' => 'The other fees must be at least 0.',
            'total_amount.numeric' => 'The total amount must be a number.',
            'total_amount.min' => 'The total amount must be at least 0.',
            'discount.numeric' => 'The discount must be a number.',
            'discount.min' => 'The discount must be at least 0.',
            'net_amount.numeric' => 'The net amount must be a number.',
            'net_amount.min' => 'The net amount must be at least 0.',
            'status.required' => 'The status field is required.',
            'status.in' => "The status must be either 'draft' or 'finalized'.",
        ];
    }
}
