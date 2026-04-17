<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Remove email from students table.
     * Single source of truth: users.email via students.user_id relationship
     *
     * Access email via: $student->user->email
     */
    public function up(): void
    {
        if (Schema::hasColumn('students', 'email')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('email')->unique()->after('last_name');
        });
    }
};
