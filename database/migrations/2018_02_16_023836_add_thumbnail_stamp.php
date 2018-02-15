<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddThumbnailStamp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stamps', function (Blueprint $table) {
            $table->string('thumbnail', 128)
                ->nullable()
                ->after('name');

            // インデックス
            $table->index('thumbnail');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stamps', function (Blueprint $table) {
            $table->dropIndex('stamps_thumbnail_index');

            $table->dropColumn('thumbnail');
        });
    }
}
