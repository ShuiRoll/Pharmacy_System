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
        Schema::create('outbound_line_items', function (Blueprint $table) {
        $table->id('outbound_lineID');
        $table->foreignId('out_transactionID')->constrained('outbound_transactions', 'out_transactionID');
        $table->foreignId('batchID')->constrained('inventory_batches', 'batchID');
        $table->integer('quantity_dispensed');
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbound_line_items');
    }
};
