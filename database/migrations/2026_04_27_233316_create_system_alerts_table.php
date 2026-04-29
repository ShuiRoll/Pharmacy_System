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
        Schema::create('system_alerts', function (Blueprint $table) {
        $table->id('alertID');
        $table->foreignId('itemID')->constrained('items', 'itemID');
        $table->foreignId('batchID')->nullable()->constrained('inventory_batches', 'batchID');
        $table->string('alert_type');           
        $table->boolean('is_resolved')->default(false);
        $table->timestamp('date_generated')->useCurrent();
        $table->timestamp('resolved_at')->nullable();
        $table->foreignId('resolved_by')->nullable()->constrained('users');
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_alerts');
    }
};
