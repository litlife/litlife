<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUrlShortsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('url_shorts', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('key')->unique()->comment(__('url_short.key'));
			$table->text('url')->unique()->comment(__('url_short.url'));
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('url_shorts');
	}
}
