<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessagesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('messages')) {
			Schema::create('messages', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->boolean('is_read')->default(0);
				$table->boolean('recepient_del')->nullable()->default(0);
				$table->boolean('sender_del')->nullable()->default(0);
				$table->integer('recepient_id')->nullable()->default(0);
				$table->integer('create_user_id')->default(0);
				$table->text('text');
				$table->integer('create_time')->default(0);
				$table->boolean('is_spam')->default(0);
				$table->text('bb_text')->nullable();
				$table->timestamps();
				$table->softDeletes();
				$table->smallInteger('new')->default(1)->comment('Если 1, то сообщение новое (не прочитано), если 0, то прочитано');
				$table->boolean('image_size_defined')->default(0)->index();
				$table->boolean('external_images_downloaded')->default(0);
				$table->integer('conversation_id')->nullable()->index();
				$table->dateTime('deleted_at_for_created_user')->nullable();
				$table->dateTime('user_updated_at')->nullable();
				$table->index(['create_user_id', 'sender_del'], 'messages_sender_id_sender_del_index');
				$table->index(['conversation_id', 'created_at']);
				$table->index(['create_user_id', 'recepient_id', 'sender_del'], 'messages_sender_id_recepient_id_sender_del_index');
				$table->index(['recepient_id', 'recepient_del']);
				$table->index(['create_user_id', 'recepient_id', 'recepient_del'], 'messages_sender_id_recepient_id_recepient_del_index');
				$table->unique(['created_at', 'id']);
			});
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('messages');
	}

}
