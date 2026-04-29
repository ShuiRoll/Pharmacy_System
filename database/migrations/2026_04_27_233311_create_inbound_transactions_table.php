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
        Schema::create('inbound_transactions', function (Blueprint $table) {
        $table->id('in_transactionID');
        $table->foreignId('poID')->nullable()->constrained('purchase_orders', 'poID');
        $table->foreignId('userID')->constrained('users');
        $table->string('quality_status')->default('Pending');
        $table->date('date_received');
        $table->decimal('total_cost', 10, 2)->default(0);
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbound_transactions');
    }
};
