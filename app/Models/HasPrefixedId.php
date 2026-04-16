<?php

namespace App\Models;

use Illuminate\Support\Str;

trait HasPrefixedId
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected static function bootHasPrefixedId(): void
    {
        static::creating(function (self $model): void {
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
