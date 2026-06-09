<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('enrollment_id');
            $table->uuid('task_id')->index();
            $table->string('file_url', 500)->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'revisi'])->default('menunggu');
            $table->text('feedback')->nullable();
            $table->unsignedTinyInteger('nilai')->nullable();
            $table->uuid('reviewed_by')->nullable()->index();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(['enrollment_id', 'task_id'], 'submissions_enrollment_task_unique');
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('mentor')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
