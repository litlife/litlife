<?php

use Illuminate\Database\Migrations\Migration;

class TransformUserGroups extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Illuminate\Support\Facades\DB::table('users')
			->chunkById(1000, function ($users) {

				foreach ($users as $user) {

					echo($user->id . "\n");

					DB::table('user_group_pivot')
						->updateOrInsert(
							['user_id' => $user->id, 'user_group_id' => $user->user_group_id],
							['user_id' => $user->id, 'user_group_id' => $user->user_group_id]
						);
				}
			});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}
}
