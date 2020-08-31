<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePasswordResetsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('password_resets')) {
			Schema::create('password_resets', function (Blueprint $table) {
				$table->integer('id', true);
				$table->integer('user_id');
				$table->string('email');
				$table->string('token', 32);
				$table->dateTime('used_at')->nullable();
				$table->timestamps();
				$table->softDeletes();
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
		Schema::drop('password_resets');
	}

}
