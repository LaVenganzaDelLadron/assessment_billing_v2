<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PaymentMethodsRequest extends CrudRequest
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
        if($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('payment_methods', 'name')->ignore($this->routeId())],
            ];
        }
        return [];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name has already been taken.',
        ];
    }
}
