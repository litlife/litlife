<?php

use Illuminate\Database\Migrations\Migration;

class AlterBookGroupsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		/*
		Schema::table('book_groups', function (Blueprint $table) {

		   $table->float('vote_average', 10, 0)->nullable()->default(0)->comment(__('book_group.vote_average'));
		   $table->smallInteger('user_vote_count')->nullable()->comment(__('book_group.user_vote_count'));
		   $table->text('rate_info')->nullable()->comment(__('book_group.vote_info'));

		   $table->integer('user_read_count')->nullable()->comment(__('book_group.user_read_count'));
		   $table->integer('user_read_later_count')->nullable()->comment(__('book_group.user_read_later_count'));
		   $table->integer('user_read_now_count')->nullable()->comment(__('book_group.user_read_now_count'));
		   $table->integer('user_read_not_complete_count')->nullable()->comment(__('book_group.user_read_not_complete_count'));
		   $table->integer('user_read_not_read_count')->nullable()->comment(__('book_group.user_read_not_read_count'));
		   $table->smallInteger('male_vote_count')->nullable()->comment(__('book_group.male_vote_count'));
		   $table->smallInteger('female_vote_count')->nullable()->comment(__('book_group.female_vote_count'));
		});
		*/
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		/*
		Schema::table('book_groups', function (Blueprint $table) {
		   $table->dropColumn('vote_average');
		   $table->dropColumn('user_vote_count');
		   $table->dropColumn('rate_info');
		   $table->dropColumn('user_read_count');
		   $table->dropColumn('user_read_later_count');
		   $table->dropColumn('user_read_now_count');
		   $table->dropColumn('user_read_not_complete_count');
		   $table->dropColumn('user_read_not_read_count');
		   $table->dropColumn('male_vote_count');
		   $table->dropColumn('female_vote_count');
		});
		*/
	}
}
