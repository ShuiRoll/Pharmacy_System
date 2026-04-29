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
        Schema::create('return_lines', function (Blueprint $table) {
        $table->id('return_lineID');
        $table->foreignId('returnID')->constrained('sale_returns', 'returnID');
        $table->foreignId('sale_lineID')->constrained('sale_lines', 'sale_lineID');
        $table->integer('quantity_returned');
        $table->decimal('refund_amount', 10, 2);
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_lines');
    }
};
