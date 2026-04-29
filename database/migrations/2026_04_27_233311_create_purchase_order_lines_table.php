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
       Schema::create('purchase_order_lines', function (Blueprint $table) {
        $table->id('po_lineID');
        $table->foreignId('poID')->constrained('purchase_orders', 'poID');
        $table->foreignId('itemID')->constrained('items', 'itemID');
        $table->integer('quantity_ordered');
        $table->decimal('unit_cost', 10, 2);
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_lines');
    }
};
