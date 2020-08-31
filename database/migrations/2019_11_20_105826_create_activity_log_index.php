<?php

use Illuminate\Database\Migrations\Migration;

class CreateActivityLogIndex extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Illuminate\Support\Facades\DB::statement(
			'create index activity_log_created_at_desc_index' .
			' on activity_log (created_at desc);'
		);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		\Illuminate\Support\Facades\DB::statement(
			'drop index activity_log_created_at_desc_index'
		);
	}
}
