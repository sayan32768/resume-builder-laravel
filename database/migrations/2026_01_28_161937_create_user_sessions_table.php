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
        Schema::create('user_sessions', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();

            $table->uuid('user_id');
            $table->unsignedBigInteger('access_token_id')->nullable();
            $table->unsignedBigInteger('refresh_token_id')->nullable();

            $table->string('ip_address', 64)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('last_seen_at')->nullable();

            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['access_token_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
