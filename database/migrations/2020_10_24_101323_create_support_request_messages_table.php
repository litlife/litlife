<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportRequestMessagesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('support_request_messages', function (Blueprint $table) {
			$table->id();
			$table->integer('support_request_id')->comment(__('support_request_message.create_user_id'));
			$table->integer('create_user_id')->comment(__('support_request_message.create_user_id'));
			$table->text('text')->comment(__('support_request_message.text'));
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
		Schema::dropIfExists('support_request_messages');
	}
}
