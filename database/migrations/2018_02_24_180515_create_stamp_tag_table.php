<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStampTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stamp_tag', function (Blueprint $table) {
            $table->unsignedInteger('stamp_id');
            $table->unsignedInteger('tag_id');
            $table->unsignedInteger('room_id'); // 逆正規化
            $table->ipAddress('ip'); // 荒らし対策
            $table->timestamps();
        });

        // 外部キー作成
        Schema::table('stamp_tag', function (Blueprint $table) {
            $table->foreign('stamp_id')->references('id')->on('stamps');
            $table->foreign('tag_id')->references('id')->on('tags');
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
        Schema::table('stamp_tag', function (Blueprint $table) {
            $table->dropForeign('stamp_tag_room_id_foreign');
            $table->dropForeign('stamp_tag_tag_id_foreign');
            $table->dropForeign('stamp_tag_stamp_id_foreign');
        });

        Schema::dropIfExists('stamp_tag');
    }
}
