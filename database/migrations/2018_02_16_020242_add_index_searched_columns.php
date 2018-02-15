<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexSearchedColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stamps', function (Blueprint $table) {
            $table->index('hash');
            $table->index('created_at');
            $table->index('deleted_at');
        });
        Schema::table('imprints', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('comment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('imprints', function (Blueprint $table) {
            $table->dropIndex('imprints_comment_index');
            $table->dropIndex('imprints_created_at_index');
        });
        Schema::table('stamps', function (Blueprint $table) {
            $table->dropIndex('stamps_deleted_at_index');
            $table->dropIndex('stamps_created_at_index');
            $table->dropIndex('stamps_hash_index');
        });
    }
}
