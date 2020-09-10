<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('color')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });

        Schema::table('chats', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 外部キー削除
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign('chats_user_id_foreign');
        });

        Schema::dropIfExists('chats');
    }
}
