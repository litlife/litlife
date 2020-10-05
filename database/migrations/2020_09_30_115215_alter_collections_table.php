<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCollectionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('collections', function (Blueprint $table) {
			$table->text('description')->nullable()->comment(__('collection.description'))->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('collections', function (Blueprint $table) {
			$table->string('description', 255)->nullable()->comment(__('collection.description'))->change();
		});
	}
}
