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
        Schema::create('citas_canceladas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cita_id');
            $table->string('descripcion')->nullable();
            $table->unsignedInteger('cancelada_por_id');
            $table->foreign('cancelada_por_id')->references('id')->on('users');
            $table->foreign('cita_id')->references('id')->on('citas');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas_canceladas');
    }
};
