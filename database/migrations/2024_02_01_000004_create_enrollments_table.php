<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pelajar_id');
            $table->uuid('program_id')->index();
            $table->enum('status', ['aktif', 'selesai', 'dibatalkan', 'ditolak'])->default('aktif');
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();

            $table->unique(['pelajar_id', 'program_id'], 'enrollments_pelajar_program_unique');
            $table->foreign('pelajar_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
