<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropBlackListUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 外部キー削除
        Schema::table('black_list_users', function (Blueprint $table) {
            $table->dropForeign('black_list_users_target_id_foreign');
            $table->dropForeign('black_list_users_user_id_foreign');
        });

        Schema::dropIfExists('black_list_users');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('black_list_users', function (Blueprint $table) {
            $table->unsignedInteger('user_id');

            // ブラックリストの対象ユーザID
            $table->unsignedInteger('target_id');

            $table->timestamps();
        });

        // 外部キー作成
        Schema::table('black_list_users', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('target_id')->references('id')->on('users');
        });
    }
}
