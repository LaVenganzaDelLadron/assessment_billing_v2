<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Seed payment_methods table with default payment method options.
     * This must run BEFORE any data migration that maps string payment_method to IDs.
     */
    public function up(): void
    {
        $methods = [
            ['code' => 'CASH', 'name' => 'Cash', 'is_active' => true],
            ['code' => 'CARD', 'name' => 'Credit/Debit Card', 'is_active' => true],
            ['code' => 'CHECK', 'name' => 'Check', 'is_active' => true],
            ['code' => 'BANK_TRANSFER', 'name' => 'Bank Transfer', 'is_active' => true],
            ['code' => 'ONLINE', 'name' => 'Online Payment', 'is_active' => true],
            ['code' => 'OTHER', 'name' => 'Other', 'is_active' => true],
        ];

        foreach ($methods as $method) {
            DB::table('payment_methods')->updateOrInsert(
                ['code' => $method['code']],
                array_merge($method, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('payment_methods')
            ->whereIn('code', ['CASH', 'CARD', 'CHECK', 'BANK_TRANSFER', 'ONLINE', 'OTHER'])
            ->delete();
    }
};
