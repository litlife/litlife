<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserEmailTokensTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_email_tokens')) {
			Schema::create('user_email_tokens', function (Blueprint $table) {
				$table->bigInteger('user_email_id')->index();
				$table->string('token')->index();
				$table->softDeletes();
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
		Schema::drop('user_email_tokens');
	}

}
