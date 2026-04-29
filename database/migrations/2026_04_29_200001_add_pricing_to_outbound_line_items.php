<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outbound_line_items', function (Blueprint $table) {
            if (! Schema::hasColumn('outbound_line_items', 'unit_price')) {
                $table->decimal('unit_price', 10, 2)->default(0)->after('quantity_dispensed');
            }

            if (! Schema::hasColumn('outbound_line_items', 'line_total')) {
                $table->decimal('line_total', 10, 2)->default(0)->after('unit_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('outbound_line_items', function (Blueprint $table) {
            if (Schema::hasColumn('outbound_line_items', 'line_total')) {
                $table->dropColumn('line_total');
            }

            if (Schema::hasColumn('outbound_line_items', 'unit_price')) {
                $table->dropColumn('unit_price');
            }
        });
    }
};
