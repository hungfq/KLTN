<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleCriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_criteria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_id')->index();
            $table->unsignedBigInteger('criteria_id')->index();
            $table->float('score_rate');

            $table->unsignedBigInteger('created_by')->index();
            $table->unsignedBigInteger('updated_by')->index();
            $table->timestamps();
            $table->timestamp('deleted_at')->default('1999-01-01');
            $table->tinyInteger('deleted')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_criteria');
    }
}
