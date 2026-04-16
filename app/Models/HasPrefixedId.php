<?php

namespace App\Models;

use Illuminate\Support\Str;

trait HasPrefixedId
{
    protected static function bootHasPrefixedId(): void
    {
        static::retrieved(function (self $model): void {
            $model->incrementing = false;
            $model->keyType = 'string';
        });

        static::creating(function (self $model): void {
            $model->incrementing = false;
            $model->keyType = 'string';

            if (! $model->getKey()) {
                $model->setAttribute($model->getKeyName(), $model->generatePrefixedId());
            }
        });
    }

    abstract protected function idPrefix(): string;

    protected function generatePrefixedId(): string
    {
        do {
            $identifier = $this->idPrefix().'-'.Str::upper(Str::random(8));
        } while (static::query()->whereKey($identifier)->exists());

        return $identifier;
    }
}
