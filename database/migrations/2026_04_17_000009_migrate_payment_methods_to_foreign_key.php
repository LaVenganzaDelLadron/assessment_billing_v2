<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Migrate existing string payment_method values to payment_method_id foreign keys.
     * This is a data migration that must happen AFTER payment_methods are seeded.
     */
    public function up(): void
    {
        // Check if payment_method column still exists (it may have been dropped in wrong order)
        if (!Schema::hasColumn('payments', 'payment_method')) {
            return; // No data to migrate, column already dropped
        }

        // Map existing string payment methods to their IDs
        $methodMap = [
            'cash' => 'CASH',
            'card' => 'CARD',
            'check' => 'CHECK',
            'bank_transfer' => 'BANK_TRANSFER',
            'bank transfer' => 'BANK_TRANSFER',
            'online' => 'ONLINE',
        ];

        foreach ($methodMap as $oldValue => $code) {
            $methodId = DB::table('payment_methods')
                ->where('code', $code)
                ->value('id');

            if ($methodId) {
                DB::table('payments')
                    ->whereRaw('LOWER(payment_method) = ?', [strtolower($oldValue)])
                    ->update(['payment_method_id' => $methodId]);
            }
        }

        // Handle any unmapped values by assigning to 'OTHER'
        $otherId = DB::table('payment_methods')
            ->where('code', 'OTHER')
            ->value('id');

        DB::table('payments')
            ->whereNull('payment_method_id')
            ->update(['payment_method_id' => $otherId]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset payment_method_id to null to allow rollback
        DB::table('payments')->update(['payment_method_id' => null]);
    }
};
