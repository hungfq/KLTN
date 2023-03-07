<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicProposalStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_proposal_students', function (Blueprint $table) {
            $table->unsignedBigInteger('topic_id');
            $table->unsignedBigInteger('student_id');

            $table->primary(['topic_id', 'student_id']);

            $table->foreign('student_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('topic_id')
                ->references('id')
                ->on('topic_proposals')
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
        Schema::dropIfExists('topic_proposal_students');
    }
}
