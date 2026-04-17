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
     * Remove tuition_per_unit from programs table.
     * Single source of truth: fee_structure with fee_type='tuition'
     *
     * Before removal, ensure all programs have tuition entries in fee_structure.
     */
    public function up(): void
    {
        // Only run migration if column exists
        if (Schema::hasColumn('programs', 'tuition_per_unit')) {

            $programs = DB::table('programs')
                ->whereNotNull('tuition_per_unit')
                ->get();

            foreach ($programs as $program) {

                $exists = DB::table('fee_structure')
                    ->where('program_id', $program->id)
                    ->where('fee_type', 'tuition')
                    ->exists();

                if (!$exists && $program->tuition_per_unit > 0) {
                    DB::table('fee_structure')->insert([
                        'program_id' => $program->id,
                        'fee_type' => 'tuition',
                        'amount' => $program->tuition_per_unit,
                        'per_unit' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            Schema::table('programs', function (Blueprint $table) {
                $table->dropColumn('tuition_per_unit');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('programs', 'tuition_per_unit')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->decimal('tuition_per_unit', 10, 2)->nullable();
            });
        }

        $tuitionFees = DB::table('fee_structure')
            ->where('fee_type', 'tuition')
            ->where('per_unit', true)
            ->get();

        foreach ($tuitionFees as $fee) {
            DB::table('programs')
                ->where('id', $fee->program_id)
                ->update(['tuition_per_unit' => $fee->amount]);
        }
    }
};
