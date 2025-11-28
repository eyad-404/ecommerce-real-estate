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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('location');
            $table->decimal('price', 15, 2);
            $table->string('status')->default('pending');
            $table->string('image');
            $table->unsignedBigInteger('user_id');
            $table->text('description');
            $table->enum('transaction_type', ['sale', 'rent']);
            $table->integer('installment_years')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // إنشاء جدول property_images المرتبط بـ property
        Schema::create('property_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_images');
        Schema::dropIfExists('properties');
    }
};
