<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOauthIdentitiesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('oauth_identities')) {
			Schema::create('oauth_identities', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->bigInteger('user_id');
				$table->string('provider_user_id');
				$table->string('provider');
				$table->string('access_token');
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
		Schema::drop('oauth_identities');
	}

}
