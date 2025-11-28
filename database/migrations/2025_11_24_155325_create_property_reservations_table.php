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
        Schema::create('property_reservations', function (Blueprint $table) {
            $table->id();

            // اللي بيحجز العقار
            $table->unsignedBigInteger('user_id');

            // العقار نفسه
            $table->unsignedBigInteger('property_id');

            // وقت الحجز
            $table->timestamp('reserved_at')->nullable();

            $table->timestamps();

            // العلاقات (Foreign keys)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_reservations');
    }
};
