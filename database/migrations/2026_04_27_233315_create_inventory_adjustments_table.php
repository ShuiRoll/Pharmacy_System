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
        Schema::create('inventory_adjustments', function (Blueprint $table) {
        $table->id('adjustmentID');
        $table->foreignId('batchID')->constrained('inventory_batches', 'batchID');
        $table->foreignId('userID')->constrained('users');   // usually Admin
        $table->date('adjustment_date');
        $table->integer('quantity_changed');
        $table->string('reason');
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_adjustments');
    }
};
