<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailingTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mailings', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('email', 100)->index()->comment('Почта');
			$table->integer('priority')->nullable()->comment('Приоритет отправки');
			$table->string('name', 256)->nullable()->comment('Имя пользователя');
			$table->timestamp('sent_at')->index()->nullable()->comment('Время отправки сообщения');
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
		Schema::dropIfExists('mailings');
	}
}
