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
        Schema::create('decisions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index();
            $table->enum('phase_type', ['request', 'response']);
            $table->unsignedBigInteger('score');
            $table->string('action');
            $table->longText('action_configuration')->nullable();
            $table->longText('description')->nullable();
            $table->foreignId('wordlist_id')->nullable()->index()->constrained('wordlists')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decisions');
    }
};
