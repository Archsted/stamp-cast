<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImprintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imprints', function (Blueprint $table) {
            $table->increments('id');

            // スタンプ送信されたルーム
            $table->unsignedInteger('room_id');

            // スタンプを送ったユーザ
            // 未登録（未ログイン）ユーザーの場合はnullとなる
            $table->unsignedInteger('user_id')->nullable();

            // 送信されたスタンプ
            // 一言スタンプの場合はnullとなる
            $table->unsignedInteger('stamp_id')->nullable();

            // 一言スタンプの文言
            // 画像のスタンプの場合はnullとなる
            $table->string('comment')->nullable();

            $table->timestamps();
        });

        // 外部キー作成
        Schema::table('imprints', function (Blueprint $table) {
            $table->foreign('room_id')->references('id')->on('rooms');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('stamp_id')->references('id')->on('stamps');
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
        Schema::table('imprints', function (Blueprint $table) {
            $table->dropForeign('imprints_room_id_foreign');
            $table->dropForeign('imprints_user_id_foreign');
            $table->dropForeign('imprints_stamp_id_foreign');
        });

        Schema::dropIfExists('imprints');
    }
}
