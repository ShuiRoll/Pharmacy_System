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
        Schema::create('cycle_count_lines', function (Blueprint $table) {
        $table->id('lineID');
        $table->foreignId('countID')->constrained('cycle_counts', 'countID');
        $table->foreignId('batchID')->constrained('inventory_batches', 'batchID');
        $table->integer('expected_quantity');
        $table->integer('actual_quantity');
        $table->integer('variance')->storedAs('actual_quantity - expected_quantity');
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cycle_count_lines');
    }
};
