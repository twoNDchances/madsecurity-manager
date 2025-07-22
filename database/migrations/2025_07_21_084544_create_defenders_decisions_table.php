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
        Schema::create('defenders_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('defender_id')->constrained('defenders')->cascadeOnDelete();
            $table->foreignId('decision_id')->constrained('decisions')->cascadeOnDelete();
            $table->boolean('status')->default(false)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defenders_decisions');
    }
};
