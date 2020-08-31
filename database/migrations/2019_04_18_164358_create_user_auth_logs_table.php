<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserAuthLogsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_auth_logs')) {
			Schema::create('user_auth_logs', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->bigInteger('user_id')->index();
				$table->string('ip')->index('user_auth_logs_ip_desc_index');
				$table->integer('time')->default(0);
				$table->timestamps();
				$table->integer('user_agent_id')->nullable();
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
		Schema::drop('user_auth_logs');
	}

}
