<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionsIndex extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Illuminate\Support\Facades\DB::statement(
			'create index collections_created_at_asc_index' .
			' on collections (created_at asc);'
		);

		\Illuminate\Support\Facades\DB::statement(
			'create index collections_created_at_desc_index' .
			' on collections (created_at desc);'
		);

		Schema::table('user_favorite_collections', function (Blueprint $table) {
			$table->unique(['collection_id', 'user_id']);
		});

		Schema::table('collected_books', function (Blueprint $table) {
			$table->unique(['collection_id', 'book_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		\Illuminate\Support\Facades\DB::statement(
			'drop index collections_created_at_asc_index'
		);

		\Illuminate\Support\Facades\DB::statement(
			'drop index collections_created_at_desc_index'
		);

		\Illuminate\Support\Facades\DB::statement(
			'alter table user_favorite_collections
    drop constraint user_favorite_collections_collection_id_user_id_unique;'
		);

		\Illuminate\Support\Facades\DB::statement(
			'alter table collected_books
    drop constraint collected_books_collection_id_book_id_unique;'
		);
	}
}
