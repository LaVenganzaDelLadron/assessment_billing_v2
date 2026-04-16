<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class InvoicesRequest extends CrudRequest
{
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'assessment_id' => ['required', 'exists:assessments,id'],
            'invoice_number' => ['required', 'string', 'max:255', Rule::unique('invoices', 'invoice_number')->ignore($this->routeId())],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'balance' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['unpaid', 'partial', 'paid', 'overdue'])],
        ];
    }
}
