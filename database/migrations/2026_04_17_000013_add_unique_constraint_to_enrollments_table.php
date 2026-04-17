<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add UNIQUE constraint to enrollments to prevent duplicate enrollments.
     * A student can only enroll in each subject once per academic term.
     */
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Add unique constraint (prevents duplicates)
            $table->unique(['student_id', 'subject_id', 'academic_term_id'], 'unique_student_subject_term');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropUnique('unique_student_subject_term');
        });
    }
};
