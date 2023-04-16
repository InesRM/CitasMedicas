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
        Schema::create('horario_trabajo', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('dia');
            $table->boolean('activo');
            $table->time('mañana_inicio');
            $table->time('mañana_fin');
            $table->time('tarde_inicio');
            $table->time('tarde_fin');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horario_trabajo');
    }
};
