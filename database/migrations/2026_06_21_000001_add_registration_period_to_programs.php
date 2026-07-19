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
        Schema::table('programs', function (Blueprint $table) {
            $table->date('registrasi_mulai')->nullable()->after('kuota');
            $table->date('registrasi_selesai')->nullable()->after('registrasi_mulai');
            $table->index(['registrasi_mulai', 'registrasi_selesai'], 'programs_reg_period_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropIndex('programs_reg_period_index');
            $table->dropColumn(['registrasi_mulai', 'registrasi_selesai']);
        });
    }
};
