<?php

namespace App\Http\Controllers;

use App\Models\Students;
use Illuminate\Http\Request;

abstract class Controller
{
    protected function authenticatedStudent(Request $request): ?Students
    {
        $user = $request->user();

        if ($user === null) {
            return null;
        }

        return Students::query()
            ->where('user_id', $user->id)
            ->first();
    }
}
