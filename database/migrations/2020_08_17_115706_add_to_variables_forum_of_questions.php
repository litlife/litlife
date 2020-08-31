<?php

use Illuminate\Database\Migrations\Migration;

class AddToVariablesForumOfQuestions extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$exists = \Illuminate\Support\Facades\DB::table('variables')
			->where('name', \App\Enums\VariablesEnum::ForumOfQuestions)
			->exists();

		if (!$exists) {
			\Illuminate\Support\Facades\DB::table('variables')
				->insert([
					'name' => \App\Enums\VariablesEnum::ForumOfQuestions,
					'value' => serialize(2)
				]);
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

	}
}
