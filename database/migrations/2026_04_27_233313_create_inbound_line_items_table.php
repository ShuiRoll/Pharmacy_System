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
        Schema::create('inbound_line_items', function (Blueprint $table) {
        $table->id('lineID');
        $table->foreignId('in_transactionID')->constrained('inbound_transactions', 'in_transactionID');
        $table->foreignId('itemID')->constrained('items', 'itemID');
        $table->foreignId('batchID')->nullable()->constrained('inventory_batches', 'batchID');
        $table->integer('quantity_received');
        $table->string('lot_number')->nullable();
        $table->date('expiration_date')->nullable();
        $table->decimal('unit_cost', 10, 2)->nullable();
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbound_line_items');
    }
};
