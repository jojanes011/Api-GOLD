<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserdata extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userdata', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('iduser')->unsigned();
            $table->foreign('iduser')->references('id')->on('users')->onDelete('cascade');
            $table->string('nombre', 256);
            $table->string('primer_apellido', 128);
            $table->string('segundo_apellido', 128);
            $table->bigInteger('celular');
            $table->datetime('fecha_nacimiento');
            $table->bigInteger('identificacion');
            $table->string('genero', 64);
            $table->string('foto', 256)->nullable();
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('userdata');
    }
}
