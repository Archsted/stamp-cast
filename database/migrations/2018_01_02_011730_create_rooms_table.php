<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->increments('id');

            // 作成ユーザー
            $table->unsignedInteger('user_id');

            // ルーム名
            $table->string('name', 255);

            // ルーム説明
            $table->text('description')->nullable();

            $table->timestamps();
        });

        // 外部キー作成
        Schema::table('rooms', function (Blueprint $table) {
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
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign('rooms_user_id_foreign');
        });

        Schema::dropIfExists('rooms');
    }
}
