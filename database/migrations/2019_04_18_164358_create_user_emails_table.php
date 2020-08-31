<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserEmailsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_emails')) {
			Schema::create('user_emails', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->bigInteger('user_id')->index();
				$table->string('email')->index('user_emails_email_trgm_index');
				$table->timestamps();
				$table->boolean('confirm')->default(0);
				$table->softDeletes();
				$table->boolean('show_in_profile')->default(0);
				$table->boolean('rescue')->default(0);
				$table->boolean('notice')->default(0);
				$table->string('domain', 50)->nullable();
				$table->index(['email', 'confirm'], 'user_emails_email_confirm_index');
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
		Schema::drop('user_emails');
	}

}
