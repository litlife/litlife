<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportQuestionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('support_questions', function (Blueprint $table) {
			$table->id();
			$table->tinyInteger('category')->nullable(__('support_question.category'));
			$table->string('title', 100)->nullable()->comment(__('support_question.title'));
			$table->integer('create_user_id')->comment(__('support_question.create_user_id'));
			$table->tinyInteger('status')->index()->nullable();
			$table->dateTime('status_changed_at')->nullable()->index();
			$table->integer('status_changed_user_id')->nullable();
			$table->integer('latest_message_id')->nullable()->comment(__('support_question.latest_message_id'));
			$table->integer('number_of_messages')->default(0)->comment(__('support_question.number_of_messages'));
			$table->timestamp('last_message_created_at')->index()->nullable()->comment(__('support_question.number_of_messages'));
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
		Schema::dropIfExists('support_questions');
	}
}
