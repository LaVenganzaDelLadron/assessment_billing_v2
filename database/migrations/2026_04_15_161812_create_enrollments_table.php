<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('academic_term_id')
                ->constrained('academic_terms')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('semester', 20)->index();
            $table->string('school_year', 9)->index();
            $table->enum('status', ['enrolled', 'dropped'])->default('enrolled')->index();
            $table->timestamps();

            $table->index(['student_id', 'school_year', 'semester']);
            $table->unique(['student_id', 'subject_id', 'academic_term_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
