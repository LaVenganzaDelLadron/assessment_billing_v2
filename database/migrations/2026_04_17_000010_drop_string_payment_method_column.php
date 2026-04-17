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
        // Drop old column safely
        if (Schema::hasColumn('payments', 'payment_method')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('payment_method');
            });
        }

        // PostgreSQL-safe NOT NULL update
        DB::statement('ALTER TABLE payments ALTER COLUMN payment_method_id SET NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE payments ALTER COLUMN payment_method_id DROP NOT NULL');

        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method')->after('amount_paid');
        });
    }
};

