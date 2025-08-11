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
            $table->boolean('important')->default(false);
            $table->boolean('periodic')->default(false);
            $table->boolean('last_status')->default(false);
            $table->text('health');
            $table->string('health_method')->default('post');
            $table->text('inspect');
            $table->string('inspect_method')->default('post');
            $table->text('apply');
            $table->string('apply_method')->default('patch');
            $table->text('revoke');
            $table->string('revoke_method')->default('delete');
            $table->text('implement');
            $table->string('implement_method')->default('patch');
            $table->text('suspend');
            $table->string('suspend_method')->default('delete');
            $table->longText('output')->nullable();
            $table->longText('description')->nullable();
            $table->string('certification')->nullable();
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
