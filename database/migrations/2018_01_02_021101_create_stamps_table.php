<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStampsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stamps', function (Blueprint $table) {
            $table->increments('id');

            // アップロードユーザー
            // 未登録（未ログイン）ユーザーの場合はnullとなる
            $table->unsignedInteger('user_id')->nullable();

            // アップロード先ルーム
            // nullは全ルーム共有、それ以外はそのルーム専用
            $table->unsignedInteger('room_id')->nullable();

            // ファイル名
            // アップロード時に一意のランダム文字列が自動で設定される
            $table->string('name', 128)->unique();

            $table->string('mime_type', 32);
            $table->integer('width', false, true);
            $table->integer('height', false, true);
            $table->integer('size', false, true);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });

        // 外部キー作成
        Schema::table('stamps', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('room_id')->references('id')->on('rooms');
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
        Schema::table('stamps', function (Blueprint $table) {
            $table->dropForeign('stamps_user_id_foreign');
            $table->dropForeign('stamps_room_id_foreign');
        });

        Schema::dropIfExists('stamps');
    }
}
