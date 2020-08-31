<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersGroupsAddEditFieldOfPublicDomain extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_groups', function (Blueprint $table) {
			$table->boolean('edit_field_of_public_domain')->default(false)->comment(__('user_group.edit_field_of_public_domain'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_groups', function (Blueprint $table) {
			$table->dropColumn('edit_field_of_public_domain');
		});
	}
}
