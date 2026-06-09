<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('enrollment_id')->unique();
            $table->uuid('issued_by')->nullable()->index();
            $table->string('nomor_sertifikat', 100)->unique();
            $table->string('pdf_url', 500)->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();

            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
            $table->foreign('issued_by')->references('id')->on('mitra')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
