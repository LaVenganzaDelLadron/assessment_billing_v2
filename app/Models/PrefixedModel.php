<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

abstract class PrefixedModel extends Model
{
    use HasApiTokens, HasFactory, HasPrefixedId, Notifiable;
}
