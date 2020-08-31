<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserSocialAccountsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_social_accounts')) {
			Schema::create('user_social_accounts', function (Blueprint $table) {
				$table->integer('id', true);
				$table->integer('user_id');
				$table->string('provider_user_id');
				$table->string('provider');
				$table->string('access_token');
				$table->text('parameters');
				$table->timestamps();
				$table->unique(['provider_user_id', 'provider']);
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
		Schema::drop('user_social_accounts');
	}

}
