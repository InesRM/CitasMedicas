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
        Schema::create('especialidad_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('especialidad_id');
            $table->unsignedInteger('user_id');
            $table->foreign('especialidad_id')->references('id')->on('especialidades');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('especialidad_user');
    }
};
