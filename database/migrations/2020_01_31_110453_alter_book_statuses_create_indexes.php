<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBookStatusesCreateIndexes extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//\Illuminate\Support\Facades\DB::statement('create index book_statuses_user_updated_at_asc_nulls_first_index on book_statuses (user_updated_at asc nulls first);');
		\Illuminate\Support\Facades\DB::statement('create index book_statuses_user_updated_at_desc_nulls_last_index on book_statuses (user_updated_at desc nulls last);');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('book_statuses', function (Blueprint $table) {
			//$table->dropIndex('book_statuses_user_updated_at_asc_nulls_first_index');
			$table->dropIndex('book_statuses_user_updated_at_desc_nulls_last_index');
		});
	}
}
