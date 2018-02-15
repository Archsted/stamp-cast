<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexIpColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // インデックス作成
        Schema::table('stamps', function (Blueprint $table) {
            $table->index('ip');
        });
        Schema::table('imprints', function (Blueprint $table) {
            $table->index('ip');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // インデックス削除
        Schema::table('imprints', function (Blueprint $table) {
            $table->dropIndex('imprints_ip_index');
        });
        Schema::table('stamps', function (Blueprint $table) {
            $table->dropIndex('stamps_ip_index');
        });
    }
}
