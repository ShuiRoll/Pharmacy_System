<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outbound_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('outbound_transactions', 'status')) {
                $table->string('status')->default('Pending')->after('destination');
            }

            if (! Schema::hasColumn('outbound_transactions', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('outbound_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('outbound_transactions', 'total_amount')) {
                $table->dropColumn('total_amount');
            }

            if (Schema::hasColumn('outbound_transactions', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
