<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexBlackListIpsIp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // インデックス作成
        Schema::table('black_list_ips', function (Blueprint $table) {
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
        // インデックス作成
        Schema::table('black_list_ips', function (Blueprint $table) {
            $table->dropIndex('black_list_ips_ip_index');
        });
    }
}
