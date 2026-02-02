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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // who did it
            $table->foreignUuid('actor_id')->nullable()->constrained('users')->nullOnDelete();

            // what happened
            $table->string('action'); // e.g. 'USER_BLOCKED'

            // what was affected
            $table->string('target_type')->nullable();  // e.g. App\Models\User
            $table->string('target_id')->nullable();    // store as string (uuid compatible)

            // change info
            $table->json('meta')->nullable();           // extra data
            $table->json('before')->nullable();
            $table->json('after')->nullable();

            // request info
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index(['action']);
            $table->index(['target_type', 'target_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
