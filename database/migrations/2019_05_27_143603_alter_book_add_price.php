<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBookAddPrice extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('books', function (Blueprint $table) {
			$table->double('price', 4, 2)->nullable()->comment('Цена книги, когда она продается. Если цены нет, то она бесплатна');
			$table->smallInteger('free_sections_count')->nullable()->comment('Количество бесплатных глав с начала книги. Если 0 - то все главы платные');
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
			$table->dropColumn('price');
			$table->dropColumn('free_sections_count');
		});
	}
}
