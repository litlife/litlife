<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSearchSettingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_search_settings', function (Blueprint $table) {
			$table->bigInteger('user_id');
			$table->string('name', 20)->comment(__('user_search_setting.name'));
			$table->string('value', 20)->comment(__('user_search_setting.value'));

			$table->unique(['user_id', 'name']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_search_settings');
	}
}
