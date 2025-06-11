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
        Schema::create('wordlists_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wordlist_id')->constrained('wordlists')->cascadeOnDelete();
            $table->foreignId('target_id')->constrained('targets')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wordlists_targets');
    }
};
