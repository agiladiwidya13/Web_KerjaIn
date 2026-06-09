<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentor', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->uuid('mitra_id')->nullable()->index();
            $table->string('profesi')->nullable();
            $table->string('perusahaan')->nullable();
            $table->unsignedTinyInteger('tahun_pengalaman')->default(0);
            $table->text('bio_keahlian')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('mitra_id')->references('id')->on('mitra')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentor');
    }
};
