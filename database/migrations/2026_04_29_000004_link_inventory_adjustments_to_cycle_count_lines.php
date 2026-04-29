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
        Schema::table('inventory_adjustments', function (Blueprint $table): void {
            if (! Schema::hasColumn('inventory_adjustments', 'cycle_count_lineID')) {
                $table->foreignId('cycle_count_lineID')
                    ->nullable()
                    ->after('reason')
                    ->constrained('cycle_count_lines', 'lineID')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_adjustments', function (Blueprint $table): void {
            if (Schema::hasColumn('inventory_adjustments', 'cycle_count_lineID')) {
                $table->dropConstrainedForeignId('cycle_count_lineID');
            }
        });
    }
};
