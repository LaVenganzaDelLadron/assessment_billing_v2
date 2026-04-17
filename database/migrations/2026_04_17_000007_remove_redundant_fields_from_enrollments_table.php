<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Remove redundant semester and school_year from enrollments.
     * These values should be queried from academic_terms via academic_term_id.
     * This eliminates data duplication and maintains single source of truth.
     */
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex(['semester']);
            $table->dropIndex(['school_year']);
            $table->dropIndex(['student_id', 'school_year', 'semester']);
            $table->dropColumn(['semester', 'school_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->string('semester', 20)->index()->after('academic_term_id');
            $table->string('school_year', 9)->index()->after('semester');
            $table->index(['student_id', 'school_year', 'semester']);
        });
    }
};
