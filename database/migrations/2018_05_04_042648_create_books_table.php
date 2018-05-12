<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('name', 32);
            $table->text('description')->nullable();
            $table->integer('order')->default(1)->index();
            $table->timestamps();
        });

        // 外部キー作成
        Schema::table('books', function (Blueprint $table) {
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
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign('books_user_id_foreign');
        });

        Schema::dropIfExists('books');
    }
}
