<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AuditLogsRequest extends CrudRequest
{
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
        return [
            'user_id' => ['nullable', 'exists:users,id'],
            'action' => ['required', 'string', 'max:255'],
            'entity_type' => ['required', 'string', 'max:255'],
            'entity_id' => ['required', 'string', 'max:255'],
            'ip_address' => ['nullable', 'ip'],
        ];
    }
}
