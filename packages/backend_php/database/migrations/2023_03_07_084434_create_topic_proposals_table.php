<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_proposals', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->nullable();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->tinyInteger('limit');
            $table->unsignedBigInteger('schedule_id')->nullable();
            $table->unsignedBigInteger('lecturer_id')->nullable();
            $table->string('status', 15)->nullable();

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
        Schema::dropIfExists('topic_proposals');
    }
}
