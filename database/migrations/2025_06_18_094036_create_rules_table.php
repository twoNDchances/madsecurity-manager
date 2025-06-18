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
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('alias')->unique()->index();
            $table->unsignedSmallInteger('phase');
            $table->longText('description')->nullable();

            $table->foreignId('target_id')->nullable()->index()->constrained('targets')->nullOnDelete();
            $table->string('comparator');
            $table->boolean('inverse')->default(false);
            $table->longText('value')->nullable();
            $table->string('action')->nullable();
            $table->longText('action_configuration')->nullable();

            $table->enum('severity', ['notice', 'warning', 'error', 'critical'])->nullable()->default('notice');

            $table->boolean('log')->default(true);
            $table->boolean('time')->default(true);
            $table->boolean('status')->default(true);
            $table->boolean('user_agent')->default(true);
            $table->boolean('client_ip')->default(true);
            $table->boolean('method')->default(true);
            $table->boolean('path')->default(true);

            $table->foreignId('user_id')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->foreignId('wordlist_id')->nullable()->index()->constrained('wordlists')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rules');
    }
};
