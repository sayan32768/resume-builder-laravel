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
        Schema::create('educations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('resumeId')->constrained('resumes')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('degree')->nullable();
            $table->string('location')->nullable();
            $table->date('startDate')->nullable();
            $table->date('endDate')->nullable();

            // JSON grades
            $table->json('grades')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education');
    }
};
