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
        Schema::create('defenders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->longText('url')->unique()->index();
            $table->boolean('status')->default(true)->nullable();
            $table->unsignedBigInteger('current')->default(0)->nullable();
            $table->text('health');
            $table->text('list');
            $table->text('update');
            $table->text('delete');
            $table->json('output')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('protection')->default(false);
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defenders');
    }
};
