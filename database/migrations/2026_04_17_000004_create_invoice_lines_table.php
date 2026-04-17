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
        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->enum('line_type', ['tuition', 'lab_fee', 'misc_fee', 'discount', 'other'])
                ->index();
            $table->foreignId('subject_id')
                ->nullable()
                ->constrained('subjects')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('description');
            $table->decimal('quantity', total: 8, places: 2)
                ->nullable()
                ->comment('Units or quantity for this line item');
            $table->decimal('unit_price', total: 10, places: 2);
            $table->decimal('amount', total: 10, places: 2)
                ->comment('quantity × unit_price or fixed amount');
            $table->timestamps();

            $table->index(['invoice_id', 'line_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_lines');
    }
};
