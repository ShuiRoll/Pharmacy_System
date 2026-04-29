<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['users', 'items', 'inventory_batches', 'suppliers', 'locations', 'purchase_orders'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            if (! Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                    $blueprint->softDeletes();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['users', 'items', 'inventory_batches', 'suppliers', 'locations', 'purchase_orders'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            if (Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                    $blueprint->dropSoftDeletes();
                });
            }
        }
    }
};