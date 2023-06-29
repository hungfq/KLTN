<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSchedulesTableAddDefaultScoreRate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->float('advisor_score_rate')->default(25)->nullable()->change();
            $table->float('critical_score_rate')->default(25)->nullable()->change();
            $table->float('president_score_rate')->default(25)->nullable()->change();
            $table->float('secretary_score_rate')->default(25)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
