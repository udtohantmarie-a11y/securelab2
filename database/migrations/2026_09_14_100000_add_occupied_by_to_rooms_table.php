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
        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table) {
                if (!Schema::hasColumn('rooms', 'occupied_by')) {
                    $table->unsignedBigInteger('occupied_by')->nullable()->after('occupancy_status');
                }
                if (!Schema::hasColumn('rooms', 'occupied_at')) {
                    $table->timestamp('occupied_at')->nullable()->after('occupied_by');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table) {
                if (Schema::hasColumn('rooms', 'occupied_at')) {
                    $table->dropColumn('occupied_at');
                }
                if (Schema::hasColumn('rooms', 'occupied_by')) {
                    $table->dropColumn('occupied_by');
                }
            });
        }
    }
};
