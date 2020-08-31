<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTextBlocksRemoveUniqueNameIndexAndAddIndex extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Illuminate\Support\Facades\DB::statement('alter table text_blocks drop constraint if exists text_blocks_name_unique;');
		\Illuminate\Support\Facades\DB::statement('drop index if exists text_blocks_name_unique;');

		Schema::table('text_blocks', function (Blueprint $table) {
			$table->index('name');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('text_blocks', function (Blueprint $table) {
			$table->dropIndex('text_blocks_name_index');
		});

		Schema::table('text_blocks', function (Blueprint $table) {
			$table->unique('name');
		});
	}
}
