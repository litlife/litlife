<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBlogsAddStatusColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('blogs', function (Blueprint $table) {
			$table->smallInteger('status')->nullable()->comment(__('blog.status'));
			$table->dateTime('status_changed_at')->nullable()->comment(__('blog.status_changed_at'));
			$table->integer('status_changed_user_id')->nullable()->comment(__('blog.status_changed_user_id'));
			$table->integer('characters_count')->nullable()->comment(__('blog.characters_count'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('blogs', function (Blueprint $table) {
			$table->dropColumn('status');
			$table->dropColumn('status_changed_at');
			$table->dropColumn('status_changed_user_id');
			$table->dropColumn('characters_count');
		});
	}
}
