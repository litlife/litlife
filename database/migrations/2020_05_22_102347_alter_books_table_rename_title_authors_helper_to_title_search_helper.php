<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBooksTableRenameTitleAuthorsHelperToTitleSearchHelper extends Migration
{
	public function __construct()
	{
		$databasePlatform = Schema::getConnection()
			->getDoctrineSchemaManager()
			->getDatabasePlatform();

		$databasePlatform->registerDoctrineTypeMapping('jsonb', 'string');
		$databasePlatform->registerDoctrineTypeMapping('_int4', 'string');
	}

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('books', function (Blueprint $table) {
			$table->renameColumn('title_authors_helper', 'title_search_helper');
		});

		Schema::table('books', function (Blueprint $table) {
			$table->text('title_search_helper')->comment(__('book.title_search_helper'))->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('books', function (Blueprint $table) {
			$table->renameColumn('title_search_helper', 'title_authors_helper');
		});

		Schema::table('books', function (Blueprint $table) {
			$table->text('title_authors_helper')->comment(__('book.title_authors_helper'))->change();
		});
	}
}
