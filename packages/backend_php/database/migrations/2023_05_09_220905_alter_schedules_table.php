<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->float('advisor_score_rate')->nullable()->after('register_end');
            $table->float('critical_score_rate')->nullable()->after('advisor_score_rate');
            $table->float('president_score_rate')->nullable()->after('critical_score_rate');
            $table->float('secretary_score_rate')->nullable()->after('president_score_rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('advisor_score_rate');
            $table->dropColumn('critical_score_rate');
            $table->dropColumn('president_score_rate');
            $table->dropColumn('secretary_score_rate');
        });
    }
}
