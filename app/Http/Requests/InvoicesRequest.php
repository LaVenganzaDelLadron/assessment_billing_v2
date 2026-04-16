<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class InvoicesRequest extends CrudRequest
{
    public function rules(): array
    {
        if($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'student_id' => ['sometimes', 'required', 'exists:students,id'],
                'assessment_id' => ['sometimes', 'required', 'exists:assessments,id'],
                'invoice_number' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('invoices', 'invoice_number')->ignore($this->routeId())],
                'total_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
                'balance' => ['sometimes', 'required', 'numeric', 'min:0'],
                'due_date' => ['sometimes', 'required', 'date'],
                'status' => ['sometimes', 'required', Rule::in(['unpaid', 'partial', 'paid', 'overdue'])],
            ];
        }
        return [];
    }
    public function messages(): array
    {
        return [
            'student_id.exists' => 'The selected student does not exist.',
            'assessment_id.exists' => 'The selected assessment does not exist.',
            'invoice_number.unique' => 'The invoice number has already been taken.',
            'total_amount.numeric' => 'The total amount must be a number.',
            'total_amount.min' => 'The total amount must be at least 0.',
            'balance.numeric' => 'The balance must be a number.',
            'balance.min' => 'The balance must be at least 0.',
            'due_date.date' => 'The due date must be a valid date.',
            'status.required' => 'The status field is required.',
            'status.in' => "The status must be one of the following: 'unpaid', 'partial', 'paid', or 'overdue'.",
        ];
    }
}
