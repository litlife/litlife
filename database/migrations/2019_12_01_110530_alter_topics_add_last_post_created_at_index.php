<?php

use Illuminate\Database\Migrations\Migration;

class AlterTopicsAddLastPostCreatedAtIndex extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Illuminate\Support\Facades\DB::statement('create index topics_last_post_created_at_desc_nulls_last_index
	on topics (last_post_created_at desc nulls last);');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		\Illuminate\Support\Facades\DB::statement('drop index if exists topics_last_post_created_at_desc_nulls_last_index');
	}
}
