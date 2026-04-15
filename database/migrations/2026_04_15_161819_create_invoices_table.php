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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('assessment_id')
                ->constrained('assessments')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('total_amount', total: 10, places: 2);
            $table->decimal('balance', total: 10, places: 2);
            $table->date('due_date')->index();
            $table->enum('status', ['unpaid', 'partial', 'paid', 'overdue'])->default('unpaid')->index();
            $table->timestamps();

            $table->index(['student_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
