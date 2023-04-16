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
        //para almacenar las citas
        Schema::create('citas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion');
            $table->date('fecha_inicio');
            $table->time('hora_inicio');
            $table->string('type');
        //para almacenar especialidad, doctor, y paciente
            $table->unsignedInteger('doctor_id');
            $table->unsignedInteger('paciente_id');
            $table->unsignedInteger('especialidad_id');
            $table ->foreign('doctor_id')->references('id')->on('users');
            $table ->foreign('paciente_id')->references('id')->on('users');
            $table ->foreign('especialidad_id')->references('id')->on('especialidades');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
