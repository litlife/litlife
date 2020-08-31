<?php

use Illuminate\Database\Migrations\Migration;

class AlterUserEmailsCreateIndexes extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Illuminate\Support\Facades\DB::statement('CREATE INDEX IF NOT EXISTS user_emails_email_trgm_index ON user_emails USING gin (email gin_trgm_ops);');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		\Illuminate\Support\Facades\DB::statement('drop index user_emails_email_trgm_index;');
	}
}
