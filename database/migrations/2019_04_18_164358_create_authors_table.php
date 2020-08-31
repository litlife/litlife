<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthorsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('authors')) {
			Schema::create('authors', function (Blueprint $table) {
				$table->integer('id', true);
				$table->string('last_name', 100)->default('')->index();
				$table->string('first_name', 100)->default('')->index('authors_first_name_trgm_idx');
				$table->string('middle_name', 100)->default('')->index();
				$table->integer('books_count')->default(0)->index();
				$table->integer('old_rating')->default(0);
				$table->string('lang', 2)->nullable();
				$table->integer('time')->default(0);
				$table->string('nickname', 50)->default('')->index();
				$table->string('home_page')->nullable();
				$table->string('email', 50)->nullable();
				$table->integer('action')->default(0);
				$table->text('description')->nullable();
				$table->integer('translate_books_count')->default(0);
				$table->integer('create_user_id')->nullable()->default(0);
				$table->smallInteger('hide')->default(0);
				$table->integer('redirect_to_author_id')->nullable()->default(0);
				$table->integer('comments_count')->default(0);
				$table->text('wikipedia_url')->nullable();
				$table->smallInteger('old_gender')->default(0);
				$table->string('born_date')->nullable();
				$table->string('born_place')->nullable();
				$table->string('dead_date')->nullable();
				$table->string('dead_place')->nullable();
				$table->string('years_creation')->nullable();
				$table->integer('edit_user_id')->nullable()->default(0);
				$table->integer('edit_time')->nullable()->default(0);
				$table->integer('hide_time')->nullable()->default(0);
				$table->integer('delete_user_id')->nullable()->default(0);
				$table->text('hide_reason')->nullable();
				$table->smallInteger('user_show')->default(0);
				$table->string('orig_last_name')->nullable();
				$table->string('orig_first_name')->nullable();
				$table->string('orig_middle_name')->nullable();
				$table->float('old_vote_average', 10, 0)->nullable()->default(0);
				$table->integer('votes_count')->default(0);
				$table->integer('forum_id')->nullable()->default(0);
				$table->integer('user_lib_count')->default(0);
				$table->integer('view_day')->default(0);
				$table->integer('view_week')->default(0);
				$table->integer('view_month')->default(0);
				$table->bigInteger('view_year')->default(0);
				$table->bigInteger('view_all')->default(0);
				$table->float('vote_average', 10, 0)->nullable()->default(0);
				$table->integer('like_count')->default(0);
				$table->integer('group_id')->nullable()->default(0)->index();
				$table->integer('group_add_user')->nullable();
				$table->integer('group_add_time')->nullable();
				$table->bigInteger('rating')->default(0)->index();
				$table->softDeletes();
				$table->timestamps();
				$table->bigInteger('photo_id')->nullable();
				$table->dateTime('view_updated_at')->nullable();
				$table->dateTime('merged_at')->nullable();
				$table->dateTime('user_edited_at')->nullable();
				$table->dateTime('accepted_at')->nullable();
				$table->dateTime('sent_for_review_at')->nullable();
				$table->integer('check_user_id')->nullable();
				$table->string('gender', 30)->default('unknown')->index();
				$table->string('name_helper', 256)->nullable()->index();
				$table->integer('biography_id')->nullable();
				$table->dateTime('rejected_at')->nullable();
				$table->smallInteger('status')->nullable();
				$table->dateTime('status_changed_at')->nullable();
				$table->integer('status_changed_user_id')->nullable();
				$table->boolean('rating_changed')->default(0)->comment('Если рейтинг у книг изменился, то значение будет true');
				$table->smallInteger('admin_notes_count')->default(0);
				$table->index(['last_name', 'first_name', 'middle_name']);
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
		Schema::drop('authors');
	}

}
