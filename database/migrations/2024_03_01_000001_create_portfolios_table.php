<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('enrollment_id')->unique();
            $table->uuid('pelajar_id')->index();
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
            $table->foreign('pelajar_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
