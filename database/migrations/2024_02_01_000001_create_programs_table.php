<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('mitra_id')->index();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('bidang', 100)->nullable();
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft')->index();
            $table->unsignedSmallInteger('kuota')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();

            $table->foreign('mitra_id')->references('id')->on('mitra')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
