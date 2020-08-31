<?php

use Illuminate\Database\Migrations\Migration;

class AddToVariablesIdeaForumId extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$exists = \Illuminate\Support\Facades\DB::table('variables')
			->where('name', \App\Enums\VariablesEnum::IdeaForum)
			->exists();

		if (!$exists) {
			\Illuminate\Support\Facades\DB::table('variables')
				->insert([
					'name' => \App\Enums\VariablesEnum::IdeaForum,
					'value' => serialize(4)
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
		\Illuminate\Support\Facades\DB::table('variables')
			->where('name', \App\Enums\VariablesEnum::IdeaForum)
			->delete();
	}
}
