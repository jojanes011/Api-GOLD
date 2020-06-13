<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComentariosEmpresas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comentarios_empresas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('publicacion_empresa')->unsigned();
            $table->foreign('publicacion_empresa')->references('id')->on('publicaciones_empresas')->onDelete('cascade');
            $table->bigInteger('creador')->unsigned();
            $table->foreign('creador')->references('id')->on('users')->onDelete('cascade');
            $table->string('contenido_comentario',256);
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
        Schema::dropIfExists('comentarios_empresas');
    }
}
