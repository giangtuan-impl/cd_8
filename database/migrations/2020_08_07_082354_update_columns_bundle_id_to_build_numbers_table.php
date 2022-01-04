<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsBundleIdToBuildNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumns('build_numbers', ['bundle_id', 'uuid_list'])) {
            Schema::table('build_numbers', function (Blueprint $table) {
                $table->string('bundle_id')->nullable()->change();
                $table->text('uuid_list')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumns('build_numbers', ['bundle_id', 'uuid_list'])) {
            Schema::table('build_numbers', function (Blueprint $table) {
                $table->integer('bundle_id')->nullable()->change();
                $table->string('uuid_list')->nullable()->change();
            });
        }
    }
}
