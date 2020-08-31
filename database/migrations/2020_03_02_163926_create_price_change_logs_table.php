<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceChangeLogsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('price_change_logs', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('book_id')->comment(__('price_change_logs.book_id'));
			$table->float('price')->nullable()->comment(__('price_change_logs.price'));
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
		Schema::dropIfExists('price_change_logs');
	}
}
