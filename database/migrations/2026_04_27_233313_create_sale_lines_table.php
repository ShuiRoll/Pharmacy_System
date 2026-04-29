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
        Schema::create('sale_lines', function (Blueprint $table) {
        $table->id('sale_lineID');
        $table->foreignId('saleID')->constrained('sales', 'saleID');
        $table->foreignId('itemID')->constrained('items', 'itemID');
        $table->foreignId('batchID')->nullable()->constrained('inventory_batches', 'batchID');
        $table->integer('quantity');
        $table->decimal('price', 10, 2);
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_lines');
    }
};
