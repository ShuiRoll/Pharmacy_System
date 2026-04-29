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
        Schema::create('inventory_batches', function (Blueprint $table) {
        $table->id('batchID');
        $table->foreignId('itemID')->constrained('items', 'itemID');
        $table->foreignId('locationID')->constrained('locations', 'locationID');
        $table->string('lot_number')->nullable();
        $table->date('expiration_date')->nullable();
        $table->integer('current_quantity')->default(0);
        $table->decimal('unit_cost', 10, 2)->nullable();
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_batches');
    }
};
