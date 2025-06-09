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
        Schema::create('composers', function (Blueprint $table) {
            $table->id();
            $table->longText('yaml');
            $table->json('resources')->nullable();
            $table->integer('pass')->nullable();
            $table->integer('fall')->nullable();
            $table->longText('output')->nullable();
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('composers');
    }
};
