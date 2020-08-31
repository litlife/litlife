<?php

use Illuminate\Database\Migrations\Migration;

class CreateAuthorsEmailsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		/*
		Schema::create('author_parsed_data', function (Blueprint $table) {
		   $table->bigIncrements('id');
		   $table->string('url', 255)->unique()->comment('Ссылка на страницу автора');
		   $table->string('name', 255)->nullable()->comment('Имя автора');
		   $table->string('email', 100)->index()->comment('Почта автора');
		   $table->string('city', 30)->nullable()->comment('Город автора');
		   $table->string('rating', 10)->nullable()->comment('Рейтинг');
		   $table->timestamps();
		});
		*/
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//Schema::dropIfExists('author_parsed_datas');
	}
}
