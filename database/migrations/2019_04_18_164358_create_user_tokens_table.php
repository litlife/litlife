<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserTokensTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_tokens')) {
			Schema::create('user_tokens', function (Blueprint $table) {
				$table->integer('user_id')->default(0)->primary('pk_user_token');
				$table->string('token', 64)->unique();
				$table->timestamps();
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
		Schema::drop('user_tokens');
	}

}
