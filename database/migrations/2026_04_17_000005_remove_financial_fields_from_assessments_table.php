<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Remove financial fields from assessments table.
     * These should be calculated in invoices and invoice_lines instead.
     * Financial totals are now the responsibility of the invoice layer.
     */
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn([
                'tuition_fee',
                'misc_fee',
                'lab_fee',
                'other_fees',
                'total_amount',
                'discount',
                'net_amount',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->decimal('tuition_fee', total: 10, places: 2)->default(0)->after('school_year');
            $table->decimal('misc_fee', total: 10, places: 2)->default(0)->after('tuition_fee');
            $table->decimal('lab_fee', total: 10, places: 2)->default(0)->after('misc_fee');
            $table->decimal('other_fees', total: 10, places: 2)->default(0)->after('lab_fee');
            $table->decimal('total_amount', total: 10, places: 2)->after('other_fees');
            $table->decimal('discount', total: 10, places: 2)->default(0)->after('total_amount');
            $table->decimal('net_amount', total: 10, places: 2)->after('discount');
        });
    }
};
