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
        // Drop foreign key first
        $table->dropForeign(['reserved_property_id']);
        // Drop the column
        $table->dropColumn('reserved_property_id');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->unsignedBigInteger('reserved_property_id')->nullable()->after('role');
        $table->foreign('reserved_property_id')
              ->references('id')->on('properties')
              ->onDelete('set null');
    });
}

};
