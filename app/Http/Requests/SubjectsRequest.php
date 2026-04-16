<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class SubjectsRequest extends CrudRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255', Rule::unique('subjects', 'code')->ignore($this->routeId())],
            'name' => ['required', 'string', 'max:255'],
            'units' => ['required', 'numeric', 'min:0'],
            'program_id' => ['required', 'exists:programs,id'],
        ];
    }
}
