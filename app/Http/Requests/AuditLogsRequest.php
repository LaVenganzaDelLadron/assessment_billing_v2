<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AuditLogsRequest extends CrudRequest
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
    protected function prepareForValidation(): void
    {
        if (! $this->filled('ip_address')) {
            $this->merge([
                'ip_address' => $this->ip(),
            ]);
        }
    }

    public function rules(): array
    {
        if($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'user_id' => ['sometimes', 'nullable', 'exists:users,id'],
                'action' => ['sometimes', 'required', 'string', 'max:255'],
                'entity_type' => ['sometimes', 'required', 'string', 'max:255'],
                'entity_id' => ['sometimes', 'required', 'string', 'max:255'],
                'ip_address' => ['sometimes', 'nullable', 'ip'],
            ];
        }
        return [];
    }
    public function messages(): array
    {
        return [
            'user_id.exists' => 'The selected user does not exist.',
            'action.required' => 'The action field is required.',
            'entity_type.required' => 'The entity type field is required.',
            'entity_id.required' => 'The entity ID field is required.',
            'ip_address.ip' => 'The IP address must be a valid IP address.',
        ];
    }
}
