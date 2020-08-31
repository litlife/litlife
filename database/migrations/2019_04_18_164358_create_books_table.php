<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBooksTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('books')) {
			Schema::create('books', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->text('genre')->nullable();
				$table->text('author')->nullable();
				$table->string('title')->index('books_book_name_index');
				$table->text('nis')->nullable();
				$table->integer('old_rating')->default(0);
				$table->integer('time_add')->default(0);
				$table->integer('page_count')->nullable()->default(0)->index();
				$table->integer('dca')->default(0);
				$table->integer('rca')->default(0);
				$table->string('ti_lb', 3)->nullable()->index();
				$table->string('ti_olb', 3)->nullable()->index();
				$table->text('pi_bn')->nullable();
				$table->text('pi_pub')->nullable();
				$table->text('pi_city')->nullable();
				$table->smallInteger('pi_year')->nullable();
				$table->text('pi_isbn')->nullable();
				$table->text('series')->nullable();
				$table->text('translator')->nullable();
				$table->integer('section_count')->default(0);
				$table->integer('action')->default(0);
				$table->integer('sum_of_votes')->default(0);
				$table->integer('create_user_id')->index();
				$table->integer('time_edit')->default(0);
				$table->integer('version')->default(0);
				$table->integer('comment_count')->default(0);
				$table->text('moderator_info')->nullable();
				$table->smallInteger('hide')->default(0);
				$table->bigInteger('redirect_to_book')->default(0);
				$table->integer('user_read_count')->default(0);
				$table->smallInteger('user_vote_count')->default(0);
				$table->text('vote_info')->nullable();
				$table->integer('user_read_later_count')->default(0);
				$table->integer('user_read_now_count')->default(0);
				$table->integer('edit_user_id')->nullable()->default(0);
				$table->integer('edit_time')->default(0);
				$table->integer('hide_time')->default(0);
				$table->integer('hide_user')->default(0);
				$table->text('hide_reason')->nullable();
				$table->smallInteger('type')->default(0);
				$table->float('old_vote_average', 10, 0)->nullable()->default(0);
				$table->smallInteger('user_show')->default(0);
				$table->integer('user_read_not_complete_count')->default(0);
				$table->text('old_formats')->nullable();
				$table->smallInteger('secret_hide')->default(0);
				$table->smallInteger('last_versions_count')->default(0);
				$table->smallInteger('google_ad_hide')->default(0);
				$table->integer('user_lib_count')->default(0);
				$table->smallInteger('ready_status')->default(0);
				$table->float('vote_average', 10, 0)->nullable()->default(0);
				$table->integer('like_count')->default(0);
				$table->smallInteger('male_vote_count')->default(0);
				$table->smallInteger('female_vote_count')->default(0);
				$table->smallInteger('swear')->default(0);
				$table->bigInteger('secret_hide_user_id')->default(0);
				$table->float('male_vote_percent', 10, 0)->nullable();
				$table->boolean('is_si')->default(0)->index();
				$table->smallInteger('in_rating')->default(0);
				$table->smallInteger('comments_closed')->default(0);
				$table->smallInteger('hide_from_top')->default(0);
				$table->smallInteger('cover_exists')->default(0);
				$table->integer('litres_id')->default(0);
				$table->integer('litres_id_by_isbn')->default(0);
				$table->smallInteger('year_writing')->nullable();
				$table->text('rightholder')->nullable()->default('');
				$table->smallInteger('year_public')->nullable();
				$table->boolean('is_public')->default(0);
				$table->smallInteger('age')->nullable()->default(0);
				$table->integer('coollib_id')->default(0);
				$table->string('secret_hide_reason')->nullable();
				$table->integer('user_read_not_read_count')->default(0);
				$table->string('lang')->nullable();
				$table->integer('year')->nullable();
				$table->softDeletes();
				$table->timestamps();
				$table->bigInteger('cover_id')->nullable()->index();
				$table->boolean('is_lp')->default(0)->index();
				$table->smallInteger('redaction')->default(0);
				$table->smallInteger('sections_count')->default(0);
				$table->text('rate_info')->nullable();
				$table->boolean('refresh_rating')->default(0);
				$table->jsonb('formats')->nullable()->index();
				$table->dateTime('accepted_at')->nullable();
				$table->integer('check_user_id')->nullable();
				$table->dateTime('connected_at')->nullable();
				$table->integer('connect_user_id')->nullable();
				$table->integer('delete_user_id')->nullable();
				$table->integer('group_id')->nullable()->index();
				$table->boolean('main_in_group')->default(0);
				//$table->integer('genres_helper')->nullable()->index();
				$table->dateTime('sent_for_review_at')->nullable();
				$table->boolean('read_access')->default(1);
				$table->boolean('download_access')->default(1);
				$table->dateTime('user_edited_at')->nullable();
				$table->boolean('need_create_new_files')->default(0);
				$table->smallInteger('attachments_count')->default(0);
				$table->smallInteger('notes_count')->default(0);
				$table->boolean('online_read_new_format')->default(1);
				$table->smallInteger('files_count')->default(0)->comment('Количество книжных файлов у книги');
				$table->boolean('annotation_exists')->default(0)->index();
				$table->boolean('is_collection')->default(0)->comment('Книга является сборником?');
				$table->boolean('images_exists')->default(0);
				$table->dateTime('rejected_at')->nullable();
				$table->smallInteger('status')->nullable();
				$table->dateTime('status_changed_at')->nullable()->index();
				$table->integer('status_changed_user_id')->nullable();
				$table->smallInteger('admin_notes_count')->default(0);
				$table->smallInteger('awards_count')->default(0);
				$table->index(['page_count', 'id']);
				$table->index(['comment_count', 'id']);
				$table->unique(['pi_year', 'id']);
				$table->unique(['year_writing', 'id']);
				$table->index(['user_read_count', 'id']);
				$table->index(['user_read_now_count', 'id']);
				$table->index(['status', 'status_changed_at']);
				$table->index(['user_vote_count', 'id']);
				$table->unique(['in_rating', 'vote_average', 'user_vote_count', 'id'], 'books_in_rating_desc_vote_average_asc_user_vote_count_desc_id_d');
				$table->unique(['in_rating', 'vote_average', 'user_vote_count', 'id'], 'books_in_rating_desc_vote_average_desc_user_vote_count_desc_id_');
			});

			\Illuminate\Support\Facades\DB::statement('alter table books add genres_helper integer[];');
			\Illuminate\Support\Facades\DB::statement('create index books_genres_helper_index on books (genres_helper);');
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('books');
	}

}
