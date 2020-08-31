<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookParsesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_parses')) {
			Schema::create('book_parses', function (Blueprint $table) {
				$table->integer('id', true);
				$table->integer('book_id')->index()->comment('ID книги над которой производилось действие');
				$table->dateTime('started_at')->nullable()->comment('Время начала парсинга');
				$table->dateTime('succeed_at')->nullable()->comment('Время когда процедура успешно завершилась');
				$table->dateTime('failed_at')->nullable()->comment('Время когда когда произошла ошибка во время процедуры');
				$table->text('parse_errors')->nullable()->comment('Ошибки которые появились при обработке');
				$table->text('options')->nullable()->comment('Опции которые будут отправлены в обработчик');
				$table->timestamps();
				$table->dateTime('waited_at')->nullable()->index();
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
		Schema::drop('book_parses');
	}

}
