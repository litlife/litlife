<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserEmilsRemoveUserEmailsEmailConfirmUniqueIndex extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//\Illuminate\Support\Facades\DB::statement('alter table user_emails drop constraint user_emails_email_confirm_unique;');
		\Illuminate\Support\Facades\DB::statement('drop index IF EXISTS user_emails_email_confirm_unique;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_emails', function (Blueprint $table) {
			$table->unique(['email', 'confirm']);
		});
	}
}
