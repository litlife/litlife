<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthorGroupsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('author_groups')) {
			Schema::create('author_groups', function (Blueprint $table) {
				$table->integer('id', true);
				$table->string('last_name')->nullable();
				$table->string('first_name')->nullable();
				$table->integer('create_user_id')->nullable();
				$table->integer('time')->nullable();
				$table->integer('count')->default(0);
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
		Schema::drop('author_groups');
	}

}
