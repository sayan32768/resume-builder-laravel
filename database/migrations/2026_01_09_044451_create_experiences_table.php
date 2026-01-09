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
        Schema::create('experiences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('resumeId')->constrained('resumes')->cascadeOnDelete();
            $table->string('companyName')->nullable();
            $table->string('companyAddress')->nullable();
            $table->string('position')->nullable();
            $table->date('startDate')->nullable();
            $table->date('endDate')->nullable();
            $table->text('workDescription')->nullable();
            $table->enum('category', ['professional', 'other']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
