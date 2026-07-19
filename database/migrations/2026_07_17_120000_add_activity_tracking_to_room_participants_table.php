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
        Schema::table('room_participants', function (Blueprint $table) {
            // Ilang beses umalis sa app ang student habang may quiz
            $table->integer('left_app_count')->default(0)->after('is_finished');
            // Kasalukuyang estado ng app ng student: active | left_app
            $table->string('app_state')->default('active')->after('left_app_count');
            $table->timestamp('last_left_at')->nullable()->after('app_state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_participants', function (Blueprint $table) {
            $table->dropColumn(['left_app_count', 'app_state', 'last_left_at']);
        });
    }
};
