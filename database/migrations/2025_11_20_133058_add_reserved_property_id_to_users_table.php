<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('reserved_property_id')->nullable()->after('role');

            // Add foreign key to properties table
            $table->foreign('reserved_property_id')
                  ->references('id')->on('properties')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['reserved_property_id']);
            $table->dropColumn('reserved_property_id');
        });
    }
};
