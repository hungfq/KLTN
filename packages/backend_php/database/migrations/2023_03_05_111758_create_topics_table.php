<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->string('code', 15);
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->tinyInteger('limit');
            $table->dateTime('thesis_defense_date')->nullable();
            $table->unsignedBigInteger('schedule_id')->nullable();
            $table->unsignedBigInteger('lecturer_id')->nullable();
            $table->unsignedBigInteger('critical_id')->nullable();
            $table->boolean('lecturer_approved')->default(false);
            $table->boolean('critical_approved')->default(false);
            $table->float('lecturer_grade')->nullable();
            $table->float('critical_grade')->nullable();
            $table->float('committee_president_grade')->nullable();
            $table->float('committee_secretary_grade')->nullable();

            $table->unsignedBigInteger('created_by')->index();
            $table->unsignedBigInteger('updated_by')->index();
            $table->timestamps();
            $table->timestamp('deleted_at')->default('1999-01-01');
            $table->tinyInteger('deleted')->default('0');

            $table->foreign('schedule_id')
                ->references('id')
                ->on('schedules')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('topics');
    }
}
