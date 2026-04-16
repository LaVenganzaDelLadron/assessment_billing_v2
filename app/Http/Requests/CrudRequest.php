<?php

namespace App\Http\Requests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

abstract class CrudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedData(): array
    {
        return $this->validated();
    }

    protected function routeId(): ?string
    {
        $route = $this->route();

        if ($route === null) {
            return null;
        }

        foreach ($route->parameters() as $parameter) {
            if ($parameter instanceof Model) {
                return (string) $parameter->getKey();
            }

            if (is_scalar($parameter)) {
                return (string) $parameter;
            }
        }

        return null;
    }
}
