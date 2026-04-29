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
        Schema::create('outbound_transactions', function (Blueprint $table) {
            $table->id('out_transactionID');
            $table->foreignId('userID')->constrained('users');   // staff who processed the outbound
            $table->date('transaction_date');
            $table->string('destination');   // e.g., "Branch 5 - Ma-a", "Customer Sale"
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbound_transactions');
    }
};
