<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailChangesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('email_changes')) {
			Schema::create('email_changes', function (Blueprint $table) {
				$table->string('email');
				$table->integer('user_id')->default(0);
				$table->string('code');
				$table->integer('time')->default(0);
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
		Schema::drop('email_changes');
	}

}
