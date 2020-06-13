<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsGruposComunidades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats_grupos_comunidades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('grupo_comunidad')->unsigned();
            $table->foreign('grupo_comunidad')->references('id')->on('grupos_comunidades')->onDelete('cascade');
            $table->bigInteger('emisor')->unsigned();
            $table->foreign('emisor')->references('id')->on('users')->onDelete('cascade');
            $table->longText('mensaje');
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
        Schema::dropIfExists('chats_grupos_comunidades');
    }
}
