<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSequencesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('sequences')) {
			Schema::create('sequences', function (Blueprint $table) {
				$table->integer('id', true);
				$table->string('name', 256)->index('sequences_name_trgm_index');
				$table->integer('create_user_id')->nullable()->index();
				$table->smallInteger('hide')->default(0);
				$table->integer('merged_to')->nullable()->default(0);
				$table->integer('book_count')->default(0);
				$table->integer('update_time')->default(0);
				$table->integer('hide_time')->default(0);
				$table->integer('hide_user')->default(0);
				$table->text('hide_reason')->nullable();
				$table->integer('user_lib_count')->default(0);
				$table->integer('like_count')->default(0);
				$table->text('description')->nullable();
				$table->timestamps();
				$table->softDeletes();
				$table->dateTime('user_edited_at')->nullable();
				$table->dateTime('accepted_at')->nullable();
				$table->dateTime('sent_for_review_at')->nullable();
				$table->integer('check_user_id')->nullable();
				$table->integer('delete_user_id')->nullable()->comment('ID пользователя который удалил серию');
				$table->integer('merge_user_id')->nullable();
				$table->dateTime('merged_at')->nullable();
				$table->dateTime('rejected_at')->nullable();
				$table->smallInteger('status')->nullable();
				$table->dateTime('status_changed_at')->nullable();
				$table->integer('status_changed_user_id')->nullable();
				$table->index(['name', 'id']);
				$table->index(['status', 'status_changed_at']);
				$table->index(['book_count', 'id']);
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
		Schema::drop('sequences');
	}

}
