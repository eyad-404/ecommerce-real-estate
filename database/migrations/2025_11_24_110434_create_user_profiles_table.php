<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('user_profiles', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->string('profile_image')->nullable();

        $table->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');

        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('user_profiles');
}

};
