<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookFilesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_files')) {
			Schema::create('book_files', function (Blueprint $table) {
				$table->bigInteger('book_id')->default(0)->index();
				$table->string('name', 256);
				$table->integer('size')->default(0);
				$table->string('format', 6);
				$table->integer('file_size')->default(0);
				$table->integer('add_time')->nullable()->default(0);
				$table->integer('create_user_id')->nullable()->default(0);
				$table->string('md5');
				$table->boolean('original')->default(0);
				$table->bigInteger('id', true);
				$table->smallInteger('hide')->default(0);
				$table->integer('hide_time')->default(0);
				$table->integer('hide_user')->default(0);
				$table->smallInteger('version')->default(0);
				$table->bigInteger('download_count')->default(0);
				$table->integer('download_count_update_time')->default(0);
				$table->text('comment')->nullable();
				$table->smallInteger('number')->nullable()->default(0);
				$table->integer('edit_time')->nullable();
				$table->integer('edit_user')->nullable();
				$table->smallInteger('name_change')->nullable()->default(0);
				$table->integer('action')->nullable()->default(0);
				$table->text('error')->nullable();
				$table->timestamps();
				$table->softDeletes();
				$table->dateTime('accepted_at')->nullable();
				$table->dateTime('sent_for_review_at')->nullable();
				$table->string('storage', 30)->default('old');
				$table->string('dirname')->nullable();
				$table->boolean('source')->default(0);
				$table->integer('check_user_id')->nullable()->comment('ID пользователя который проверил');
				$table->dateTime('rejected_at')->nullable();
				$table->smallInteger('status')->nullable();
				$table->dateTime('status_changed_at')->nullable();
				$table->integer('status_changed_user_id')->nullable();
				$table->index(['status', 'status_changed_at']);
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
		Schema::drop('book_files');
	}

}
