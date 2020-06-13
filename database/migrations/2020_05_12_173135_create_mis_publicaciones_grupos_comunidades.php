<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMisPublicacionesGruposComunidades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mis_publicaciones_grupos_comunidades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('publi_gru_comunidad')->unsigned();
            $table->foreign('publi_gru_comunidad')->references('id')->on('publicaciones_grupos_comunidades')->onDelete('cascade');
            $table->bigInteger('user')->unsigned();
            $table->foreign('user')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('mis_publicaciones_grupos_comunidades');
    }
}
