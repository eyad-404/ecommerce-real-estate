<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the profile_image column
            if (Schema::hasColumn('users', 'profile_image')) {
                $table->dropColumn('profile_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add it back if needed
            $table->string('profile_image')->nullable()->after('password');
        });
    }
};
