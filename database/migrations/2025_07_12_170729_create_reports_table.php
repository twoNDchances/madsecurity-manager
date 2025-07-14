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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('defender_id')->nullable()->index()->constrained('defenders')->nullOnDelete();
            $table->dateTime('time');
            $table->json('output');
            $table->string('user_agent');
            $table->string('client_ip');
            $table->string('method');
            $table->string('path');
            $table->json('target_ids');
            $table->foreignId('rule_id')->nullable()->index()->constrained('rules')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
