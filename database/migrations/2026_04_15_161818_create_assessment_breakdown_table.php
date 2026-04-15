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
        Schema::create('assessment_breakdown', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')
                ->constrained('assessments')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->enum('source_type', ['subject', 'fee', 'discount'])->index();
            $table->unsignedBigInteger('source_id')->nullable()->index();
            $table->string('description');
            $table->decimal('units', total: 5, places: 2)->nullable();
            $table->decimal('rate', total: 10, places: 2)->nullable();
            $table->decimal('amount', total: 10, places: 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_breakdown');
    }
};
