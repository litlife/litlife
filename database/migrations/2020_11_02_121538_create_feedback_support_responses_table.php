<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbackSupportResponsesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feedback_support_responses', function (Blueprint $table) {
			$table->id();
			$table->integer('support_question_id')->index()->comment(__('feedback_support_response.support_question_id'));
			$table->text('text')->nullable()->comment(__('feedback_support_response.text'));
			$table->tinyInteger('face_reaction')->nullable()->comment(__('feedback_support_response.face_reaction'));
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
		Schema::dropIfExists('feedback_support_responses');
	}
}
