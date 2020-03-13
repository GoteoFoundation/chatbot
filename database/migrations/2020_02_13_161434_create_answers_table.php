<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('answer_i18n');
            $table->foreign('answer_i18n')->references('translation_id')->on('translations');
            $table->dropForeign(['answer_i18n']);
            $table->unsignedInteger('order');
            $table->unsignedBigInteger('parent_question_id')->nullable();
            $table->foreign('parent_question_id')->references('id')->on('questions');
            $table->unsignedBigInteger('answer_question_id')->nullable();
            $table->foreign('answer_question_id')->references('id')->on('questions');
            $table->unsignedBigInteger('url_i18n')->nullable()->unsigned();
            $table->foreign('url_i18n')->references('translation_id')->on('translations');
            $table->dropForeign(['url_i18n']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answers');
    }
}
