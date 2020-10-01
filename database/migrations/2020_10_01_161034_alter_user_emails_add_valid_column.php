<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserEmailsAddValidColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_emails', function (Blueprint $table) {
			$table->boolean('is_valid')->nullable()->comment(__('user_email.is_valid'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_emails', function (Blueprint $table) {
			$table->dropColumn('is_valid');
		});
	}
}
