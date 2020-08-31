<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCollectionsAddLastestUpdatesAtColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('collections', function (Blueprint $table) {
			$table->timestamp('latest_updates_at')->nullable()->comment(__('collection.latest_updates_at'));
		});

		\Illuminate\Support\Facades\DB::statement('create index collection_latest_updates_at_index_desc_nulls_last on collections (latest_updates_at desc nulls last);');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('collections', function (Blueprint $table) {
			$table->dropColumn('latest_updates_at');
		});

		\Illuminate\Support\Facades\DB::statement('drop index if exists collection_latest_updates_at_index_desc_nulls_last');
	}
}
