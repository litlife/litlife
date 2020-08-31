<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAuthorsAddIndexCreatedAtWithNulls extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Illuminate\Support\Facades\DB::statement('create index authors_created_at_desc_nulls_last_index on authors (created_at desc nulls last);');
		\Illuminate\Support\Facades\DB::statement('create index authors_created_at_asc_nulls_first_index on authors (created_at asc nulls first);');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('authors', function (Blueprint $table) {
			$table->dropIndex('authors_created_at_desc_nulls_last_index');
			$table->dropIndex('authors_created_at_asc_nulls_first_index');
		});
	}
}
