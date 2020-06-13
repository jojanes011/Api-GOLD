<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGruposEmpresas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupos_empresas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre',256);
            $table->string('categoria',256);
            $table->string('descripcion',256);
            $table->string('foto', 256)->nullable();
            $table->bigInteger('creador')->unsigned();
            $table->foreign('creador')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('empresa')->unsigned();
            $table->foreign('empresa')->references('id')->on('empresas')->onDelete('cascade');
            $table->timestamps();
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
        Schema::dropIfExists('grupos_empresas');
    }
}
