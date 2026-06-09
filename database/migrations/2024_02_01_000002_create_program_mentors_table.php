<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_mentors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('program_id');
            $table->uuid('mentor_id');
            $table->timestamp('assigned_at')->nullable();

            $table->unique(['program_id', 'mentor_id'], 'program_mentors_unique');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('mentor_id')->references('id')->on('mentor')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_mentors');
    }
};
