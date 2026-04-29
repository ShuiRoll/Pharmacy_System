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
        Schema::create('items', function (Blueprint $table) {
        $table->id('itemID');
        $table->string('item_code')->unique();
        $table->string('name');
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2)->default(0.00);
        $table->integer('minimum_stock_lvl')->default(50);
        $table->string('category')->nullable();
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
