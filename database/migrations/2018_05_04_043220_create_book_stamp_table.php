<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookStampTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_stamp', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('book_id');
            $table->unsignedInteger('stamp_id');
            $table->integer('order')->default(1)->index();
            $table->timestamps();
        });

        // 外部キー作成
        Schema::table('book_stamp', function (Blueprint $table) {
            $table->foreign('book_id')->references('id')->on('books');
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
        Schema::table('book_stamp', function (Blueprint $table) {
            $table->dropForeign('book_stamp_stamp_id_foreign');
            $table->dropForeign('book_stamp_book_id_foreign');
        });

        Schema::dropIfExists('book_stamp');
    }
}
