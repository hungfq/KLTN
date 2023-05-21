<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('topic_id')->index();
            $table->unsignedBigInteger('criteria_id')->index();
            $table->unsignedBigInteger('student_id')->index();
            $table->float('score');
            $table->unsignedBigInteger('graded_by')->index();

            $table->unsignedBigInteger('created_by')->index();
            $table->unsignedBigInteger('updated_by')->index();
            $table->timestamps();
            $table->timestamp('deleted_at')->default('1999-01-01');
            $table->tinyInteger('deleted')->default('0');

                        
            $table->foreign('topic_id')
                ->references('id')
                ->on('topics')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('criteria_id')
                ->references('id')
                ->on('criteria')
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
        Schema::dropIfExists('grades');
    }
}
