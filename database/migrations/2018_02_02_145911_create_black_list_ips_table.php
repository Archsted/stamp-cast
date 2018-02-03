<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlackListIpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('black_list_ips', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->ipAddress('ip');
            $table->timestamps();
        });

        // 外部キー作成
        Schema::table('black_list_ips', function (Blueprint $table) {
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
        Schema::table('black_list_ips', function (Blueprint $table) {
            $table->dropForeign('black_list_ips_user_id_foreign');
        });

        Schema::dropIfExists('black_list_ips');
    }
}
