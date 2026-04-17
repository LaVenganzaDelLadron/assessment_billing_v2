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
        Schema::table('programs', function (Blueprint $table) {
            if (! Schema::hasColumn('programs', 'code')) {
                $table->string('code')->nullable()->after('id');
            }
            if (! Schema::hasColumn('programs', 'status')) {
                $table->string('status')->default('active')->after('department');
            }
        });

        Schema::table('subjects', function (Blueprint $table) {
            if (! Schema::hasColumn('subjects', 'subject_code')) {
                $table->string('subject_code')->nullable()->after('code');
            }
            if (! Schema::hasColumn('subjects', 'type')) {
                $table->string('type')->nullable()->after('units');
            }
            if (! Schema::hasColumn('subjects', 'status')) {
                $table->string('status')->default('active')->after('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            if (Schema::hasColumn('programs', 'code')) {
                $table->dropColumn('code');
            }
            if (Schema::hasColumn('programs', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'subject_code')) {
                $table->dropColumn('subject_code');
            }
            if (Schema::hasColumn('subjects', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('subjects', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
