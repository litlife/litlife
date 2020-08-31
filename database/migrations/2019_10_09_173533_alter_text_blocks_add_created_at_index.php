<?php

use Illuminate\Database\Migrations\Migration;

class AlterTextBlocksAddCreatedAtIndex extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Illuminate\Support\Facades\DB::statement('create index text_blocks_created_at_desc_index on text_blocks (created_at desc);');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		\Illuminate\Support\Facades\DB::statement('drop index if exists text_blocks_created_at_desc_index');
	}
}
