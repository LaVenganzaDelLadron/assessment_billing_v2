<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class InvoiceLinesRequest extends CrudRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'invoice_id' => [
                    'sometimes',
                    'required',
                    'exists:invoices,id',
                ],
                'line_type' => [
                    'sometimes',
                    'required',
                    'in:tuition,lab_fee,misc_fee,discount,other',
                ],
                'subject_id' => [
                    'sometimes',
                    'nullable',
                    'exists:subjects,id',
                ],
                'description' => ['sometimes', 'required', 'string', 'max:255'],
                'quantity' => ['sometimes', 'required', 'numeric', 'min:0.01'],
                'unit_price' => ['sometimes', 'required', 'numeric', 'min:0'],
                'amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            ];
        }

        return [];
    }

    public function messages(): array
    {
        return [
            'invoice_id.required' => 'The invoice field is required.',
            'invoice_id.exists' => 'The selected invoice does not exist.',
            'line_type.required' => 'The line type field is required.',
            'line_type.in' => 'The line type must be one of: tuition, lab_fee, misc_fee, discount, other.',
            'subject_id.exists' => 'The selected subject does not exist.',
            'description.required' => 'The description field is required.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 255 characters.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.numeric' => 'The quantity must be a number.',
            'quantity.min' => 'The quantity must be at least 0.01.',
            'unit_price.required' => 'The unit price field is required.',
            'unit_price.numeric' => 'The unit price must be a number.',
            'unit_price.min' => 'The unit price must be 0 or more.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be 0 or more.',
        ];
    }
}
