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
        Schema::create('sales', function (Blueprint $table) {
        $table->id('saleID');
        $table->foreignId('userID')->constrained('users');
        $table->string('payment_method')->default('Cash');
        $table->decimal('total', 10, 2)->default(0);
        $table->timestamp('sold_at')->useCurrent();
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
