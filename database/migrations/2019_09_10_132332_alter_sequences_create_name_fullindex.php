<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSequencesCreateNameFullindex extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Illuminate\Support\Facades\DB::statement('CREATE INDEX sequences_name_fulltext_index ON sequences USING GIN (to_tsvector(\'english\', "name"));');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sequences', function (Blueprint $table) {
			$table->dropIndex('sequences_name_fulltext_index');
		});
	}
}
