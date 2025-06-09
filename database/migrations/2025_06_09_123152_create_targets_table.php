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
        Schema::create('targets', function (Blueprint $table) {
            $table->id();
            $table->string('alias')->unique()->index();
            $table->string('type');
            $table->string('name')->index();
            // $table->string('real_name')->unique()->index();
            $table->string('engine')->nullable();
            $table->string('engine_configuration')->nullable();
            $table->integer('phase');
            $table->enum('datatype', ['string', 'number', 'array']);
            $table->enum('final_datatype', ['string', 'number', 'array']);
            $table->longText('description')->nullable();
            $table->boolean('immutable')->default(false);
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->foreignId('target_id')->nullable()->index()->constrained('targets')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
