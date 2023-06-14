<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCommitteesTableAddDefenseDateAndAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('committees', function (Blueprint $table) {
            $table->dateTime('defense_date')->nullable()->after('critical_id');
            $table->text('address')->nullable()->after('defense_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('committees', function (Blueprint $table) {
            $table->dropColumn('defense_date');
            $table->dropColumn('address');
        });
    }
}
