<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserAccountPermissionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_account_permissions')) {
			Schema::create('user_account_permissions', function (Blueprint $table) {
				$table->integer('user_id')->primary('user_account_permissions_pkey');
				$table->smallInteger('write_on_the_wall')->default(2);
				$table->smallInteger('comment_on_the_wall')->default(2);
				$table->smallInteger('write_private_messages')->default(4);
				$table->timestamps();
				$table->smallInteger('view_relations')->default(3);
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
		Schema::drop('user_account_permissions');
	}

}
