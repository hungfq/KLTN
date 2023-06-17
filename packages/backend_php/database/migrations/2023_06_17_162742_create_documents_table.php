<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('owner', 100)->index();
            $table->string('title', 255)->nullable();
            $table->string('file_name', 255);
            $table->char('file_extension', 10);
            $table->text('path');
            $table->text('des')->nullable();

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
        Schema::dropIfExists('documents');
    }
}
