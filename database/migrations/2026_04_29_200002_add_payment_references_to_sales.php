<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'gcash_reference')) {
                $table->string('gcash_reference')->nullable()->after('payment_method');
            }

            if (! Schema::hasColumn('sales', 'card_reference')) {
                $table->string('card_reference')->nullable()->after('gcash_reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'card_reference')) {
                $table->dropColumn('card_reference');
            }

            if (Schema::hasColumn('sales', 'gcash_reference')) {
                $table->dropColumn('gcash_reference');
            }
        });
    }
};
