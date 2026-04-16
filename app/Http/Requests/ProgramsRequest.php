<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ProgramsRequest extends CrudRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('programs', 'name')
                    ->where(fn ($query) => $query->where('department', $this->string('department')->toString()))
                    ->ignore($this->routeId()),
            ],
            'department' => ['required', 'string', 'max:255'],
            'tuition_per_unit' => ['required', 'numeric', 'min:0'],
        ];
    }
}
