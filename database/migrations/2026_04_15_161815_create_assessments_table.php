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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('academic_term_id')
                ->constrained('academic_terms')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('semester', 20)->index();
            $table->string('school_year', 9)->index();
            $table->decimal('total_units', total: 5, places: 2);
            $table->decimal('tuition_fee', total: 10, places: 2)->default(0);
            $table->decimal('misc_fee', total: 10, places: 2)->default(0);
            $table->decimal('lab_fee', total: 10, places: 2)->default(0);
            $table->decimal('other_fees', total: 10, places: 2)->default(0);
            $table->decimal('total_amount', total: 10, places: 2);
            $table->decimal('discount', total: 10, places: 2)->default(0);
            $table->decimal('net_amount', total: 10, places: 2);
            $table->enum('status', ['draft', 'finalized'])->default('draft')->index();
            $table->timestamps();

            $table->index(['student_id', 'school_year', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
