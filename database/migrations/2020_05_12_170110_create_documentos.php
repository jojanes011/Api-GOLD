<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentos extends Migration
{
        /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('documento',256);
            $table->bigInteger('user')->unsigned();
            $table->foreign('user')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('publicacion_comunidad')->unsigned()->nullable();
            $table->foreign('publicacion_comunidad')->references('id')->on('publicaciones_comunidades')->onDelete('cascade');
            $table->bigInteger('publicacion_grupo_comunidad')->unsigned()->nullable();
            $table->foreign('publicacion_grupo_comunidad')->references('id')->on('publicaciones_grupos_comunidades')->onDelete('cascade');
            $table->bigInteger('publicacion_empresa')->unsigned()->nullable();
            $table->foreign('publicacion_empresa')->references('id')->on('publicaciones_empresas')->onDelete('cascade');
            $table->bigInteger('publicacion_grupo_empresa')->unsigned()->nullable();
            $table->foreign('publicacion_grupo_empresa')->references('id')->on('publicaciones_grupos_empresas')->onDelete('cascade');
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
        Schema::dropIfExists('documentos');
    }
}
