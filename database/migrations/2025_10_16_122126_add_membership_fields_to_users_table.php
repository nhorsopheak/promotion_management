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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_member')->default(false)->after('password');
            $table->string('membership_tier')->nullable()->after('is_member');
            $table->timestamp('membership_started_at')->nullable()->after('membership_tier');
            $table->timestamp('membership_expires_at')->nullable()->after('membership_started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_member', 'membership_tier', 'membership_started_at', 'membership_expires_at']);
        });
    }
};
