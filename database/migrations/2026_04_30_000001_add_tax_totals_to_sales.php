<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->default(0)->after('card_reference');
            }

            if (! Schema::hasColumn('sales', 'tax_rate')) {
                $table->decimal('tax_rate', 6, 4)->default(0)->after('subtotal');
            }

            if (! Schema::hasColumn('sales', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_rate');
            }
        });

        DB::table('sales')
            ->where('subtotal', 0)
            ->where('tax_amount', 0)
            ->update(['subtotal' => DB::raw('total')]);
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'tax_amount')) {
                $table->dropColumn('tax_amount');
            }

            if (Schema::hasColumn('sales', 'tax_rate')) {
                $table->dropColumn('tax_rate');
            }

            if (Schema::hasColumn('sales', 'subtotal')) {
                $table->dropColumn('subtotal');
            }
        });
    }
};
