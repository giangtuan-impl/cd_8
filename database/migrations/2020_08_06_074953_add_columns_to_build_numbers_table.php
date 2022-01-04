<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToBuildNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('build_numbers', function (Blueprint $table) {
            $table->string('app_icon')->nullable();
            $table->integer('bundle_id')->nullable();
            $table->string('uuid_list')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumns('build_numbers', ['app_icon', 'bundle_id', 'uuid_list'])) {
            Schema::table('build_numbers', function (Blueprint $table) {
                $table->dropColumn('app_icon');
                $table->dropColumn('bundle_id');
                $table->dropColumn('uuid_list');
            });
        }
    }
}
