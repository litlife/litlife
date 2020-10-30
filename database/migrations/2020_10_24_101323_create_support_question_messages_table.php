<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportQuestionMessagesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('support_question_messages', function (Blueprint $table) {
			$table->id();
			$table->integer('support_question_id')->comment(__('support_question_message.create_user_id'));
			$table->integer('create_user_id')->comment(__('support_question_message.create_user_id'));
			$table->text('bb_text')->comment(__('support_question_message.bb_text'));
			$table->text('text')->comment(__('support_question_message.text'));
			$table->boolean('external_images_downloaded')->default(false);
			$table->integer('characters_count')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('support_question_messages');
	}
}
