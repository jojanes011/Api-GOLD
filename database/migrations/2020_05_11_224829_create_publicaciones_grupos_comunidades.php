<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicacionesGruposComunidades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicaciones_grupos_comunidades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('grupo_comunidad')->unsigned();
            $table->foreign('grupo_comunidad')->references('id')->on('grupos_comunidades')->onDelete('cascade');
            $table->bigInteger('user')->unsigned();
            $table->foreign('user')->references('id')->on('users')->onDelete('cascade');
            $table->string('title',256);
            $table->longText('contenido')->nullable();
            $table->string('foto',256)->nullable();
            $table->integer('num_likes');
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
        Schema::dropIfExists('publicaciones_grupos_comunidades');
    }
}
