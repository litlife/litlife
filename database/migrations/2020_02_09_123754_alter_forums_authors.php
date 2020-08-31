<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterForumsAuthors extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('forums', function (Blueprint $table) {
			$table->integer('obj_id')->nullable()->default(null)->change();
		});

		\Illuminate\Support\Facades\DB::table('forums')
			->where('obj_type', '1')
			->update(['obj_type' => 'author']);

		\Illuminate\Support\Facades\DB::table('forums')
			->where('obj_id', '<', '1')
			->update(['obj_id' => null]);

		\Illuminate\Support\Facades\DB::table('forums')
			->where(function ($query) {
				$query->whereNull('obj_type')
					->orWhere('obj_type', '<', '1');
			})
			->where(function ($query) {
				$query->whereNull('obj_id')
					->orWhere('obj_id', '<', '1');
			})
			->chunkById(100, function ($forums) {

				foreach ($forums as $forum) {

					if (empty($forum->obj_id) and empty($forum->obj_type)) {
						echo($forum->id . "\n");

						$author = \Illuminate\Support\Facades\DB::table('authors')
							->where('forum_id', $forum->id)
							->first();

						if (!empty($author)) {
							echo($author->id . "\n");

							\Illuminate\Support\Facades\DB::table('forums')
								->where('id', $forum->id)
								->update([
									'obj_type' => 'author',
									'obj_id' => $author->id
								]);
						}
					}
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
