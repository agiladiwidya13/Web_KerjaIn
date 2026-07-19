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
        Schema::table('program_mentors', function (Blueprint $table) {
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending')->after('mentor_id');
            $table->timestamp('applied_at')->nullable()->after('status');
            $table->timestamp('reviewed_at')->nullable()->after('applied_at');
            $table->dropColumn('assigned_at');
            $table->index('status', 'program_mentors_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_mentors', function (Blueprint $table) {
            $table->dropIndex('program_mentors_status_index');
            $table->timestamp('assigned_at')->nullable()->after('mentor_id');
            $table->dropColumn(['status', 'applied_at', 'reviewed_at']);
        });
    }
};
