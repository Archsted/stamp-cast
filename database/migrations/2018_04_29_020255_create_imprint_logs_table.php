<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImprintLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imprint_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('imprint_id');
            $table->unsignedInteger('room_id');
            $table->unsignedInteger('stamp_id');
            $table->integer('count')->default(0);
            $table->timestamps();
        });

        // 外部キー作成
        Schema::table('imprint_logs', function (Blueprint $table) {
            $table->foreign('imprint_id')->references('id')->on('imprints');
            $table->foreign('room_id')->references('id')->on('rooms');
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
        Schema::table('imprint_logs', function (Blueprint $table) {
            $table->dropForeign('imprint_logs_room_id_foreign');
            $table->dropForeign('imprint_logs_stamp_id_foreign');
            $table->dropForeign('imprint_logs_imprint_id_foreign');
        });

        Schema::dropIfExists('imprint_logs');
    }
}
