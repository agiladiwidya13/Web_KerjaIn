<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mitra', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->string('nama_usaha')->default('Belum diisi');
            $table->string('bidang_usaha')->nullable();
            $table->string('kota')->nullable();
            $table->string('kontak_bisnis')->nullable();
            $table->string('email_domain', 100);
            $table->string('logo_perusahaan')->nullable();
            $table->string('website')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('email_domain', 'mitra_email_domain_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mitra');
    }
};
