<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Poin log
        Schema::create('poin_log', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pelajar_id')->index();
            $table->smallInteger('jumlah');
            $table->string('keterangan')->nullable();
            $table->string('referensi_type', 100)->nullable();
            $table->uuid('referensi_id')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('pelajar_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Badges
        Schema::create('badges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama', 100);
            $table->string('deskripsi')->nullable();
            $table->string('icon_url')->nullable();
            $table->string('syarat')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        // User badges
        Schema::create('user_badges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('badge_id');
            $table->timestamp('earned_at')->nullable();

            $table->unique(['user_id', 'badge_id'], 'user_badges_unique');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('badge_id')->references('id')->on('badges')->onDelete('cascade');
        });

        // Messages
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sender_id')->index();
            $table->uuid('receiver_id')->index();
            $table->text('isi');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['sender_id', 'receiver_id'], 'messages_conversation_index');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('poin_log');
    }
};
