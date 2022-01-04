<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsVersionNumberAndCodeToBuildNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('build_numbers', function (Blueprint $table) {
            $table->string('version_number')->nullable();
            $table->string('version_code_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumns('build_numbers', ['version_number', 'version_code_number'])) {
            Schema::table('build_numbers', function (Blueprint $table) {
                $table->dropColumn('version_number');
                $table->dropColumn('version_code_number');
            });
        }
    }
}
