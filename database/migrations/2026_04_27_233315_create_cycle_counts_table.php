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
        Schema::create('cycle_counts', function (Blueprint $table) {
        $table->id('countID');
        $table->foreignId('userID')->constrained('users');   // staff who performed the count
        $table->date('count_date');
        $table->string('status')->default('In Progress');   // In Progress, Completed
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cycle_counts');
    }
};
