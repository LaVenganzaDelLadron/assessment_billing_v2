<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drop the old string payment_method column.
     * DATA MUST BE MIGRATED TO payment_method_id FIRST.
     * Make payment_method_id NOT NULL after this migration.
     */
    public function up(): void
    {
        // Only drop if column exists
        if (Schema::hasColumn('payments', 'payment_method')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('payment_method');
            });
        }

        // Update payment_method_id to NOT NULL in a separate operation
        DB::statement('ALTER TABLE payments MODIFY payment_method_id BIGINT UNSIGNED NOT NULL;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Make payment_method_id nullable again
        DB::statement('ALTER TABLE payments MODIFY payment_method_id BIGINT UNSIGNED NULL;');

        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method')->after('amount_paid');
        });
    }
};

