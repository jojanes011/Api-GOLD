<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsUsuarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats_usuarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('emisor')->unsigned();
            $table->foreign('emisor')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('receptor')->unsigned();
            $table->foreign('receptor')->references('id')->on('users')->onDelete('cascade');
            $table->longText('mensaje');
            $table->boolean('leido');
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
        Schema::dropIfExists('chats_usuarios');
    }
}
