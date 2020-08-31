<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFulltextIndexes extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Illuminate\Support\Facades\DB::statement('CREATE INDEX authors_name_helper_fulltext_index ON authors USING GIN (to_tsvector(\'english\', "name_helper"));');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('authors', function (Blueprint $table) {
			$table->dropIndex('authors_name_helper_fulltext_index');
		});
	}
}
