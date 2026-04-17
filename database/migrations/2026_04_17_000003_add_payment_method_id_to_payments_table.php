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
        Schema::table('payments', function (Blueprint $table) {
            // Add new FK column as nullable (data migration will follow)
            $table->foreignId('payment_method_id')
                ->nullable()
                ->after('amount_paid')
                ->constrained('payment_methods')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['payment_method_id']);
            $table->dropColumn('payment_method_id');
        });
    }
};
