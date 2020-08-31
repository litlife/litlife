<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCollectionsAddIndexes extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Illuminate\Support\Facades\DB::statement('create index collections_like_count_index_desc on collections (like_count desc, id desc);');
		\Illuminate\Support\Facades\DB::statement('create index collections_books_count_index_desc on collections (books_count desc, id desc);');
		\Illuminate\Support\Facades\DB::statement('create index collections_views_count_index_desc on collections (views_count desc, id desc);');
		\Illuminate\Support\Facades\DB::statement('create index collections_comments_count_index_desc on collections (comments_count desc, id desc);');

		\Illuminate\Support\Facades\DB::statement('create index collections_created_at_index_desc on collections (created_at desc, id desc);');
		\Illuminate\Support\Facades\DB::statement('create index collections_created_at_index_asc on collections (created_at asc, id asc);');

		\Illuminate\Support\Facades\DB::statement('drop index if exists collections_created_at_asc_index');
		\Illuminate\Support\Facades\DB::statement('drop index if exists collections_created_at_desc_index');

		\Illuminate\Support\Facades\DB::statement('create index collected_books_created_at_index_desc on collected_books (created_at desc);');
		\Illuminate\Support\Facades\DB::statement('create index collected_books_created_at_index_asc on collected_books (created_at asc);');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('collections', function (Blueprint $table) {
			$table->dropIndex('collections_like_count_index_desc');
			$table->dropIndex('collections_books_count_index_desc');
			$table->dropIndex('collections_views_count_index_desc');
			$table->dropIndex('collections_comments_count_index_desc');
			$table->dropIndex('collections_created_at_index_desc');
			$table->dropIndex('collections_created_at_index_asc');
		});

		Schema::table('collected_books', function (Blueprint $table) {
			$table->dropIndex('collected_books_created_at_index_desc');
			$table->dropIndex('collected_books_created_at_index_asc');
		});
	}
}
