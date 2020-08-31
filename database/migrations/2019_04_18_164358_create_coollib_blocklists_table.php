<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCoollibBlocklistsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('coollib_blocklists')) {
			Schema::create('coollib_blocklists', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->string('author')->nullable()->index('cb_author_idx');
				$table->string('book_name')->nullable()->index('cb_book_name_idx');
				$table->integer('time');
				$table->smallInteger('action')->default(0)->index('cb_action_idx');
				$table->boolean('is_finded')->default(0)->index('cb_is_finded_idx');
			});
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('coollib_blocklists');
	}

}
