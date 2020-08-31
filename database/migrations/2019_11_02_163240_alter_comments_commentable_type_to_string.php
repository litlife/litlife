<?php

use Illuminate\Database\Migrations\Migration;

class AlterCommentsCommentableTypeToString extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Illuminate\Support\Facades\DB::statement('alter table comments alter column commentable_type type varchar(4) using commentable_type::varchar;');

		\Illuminate\Support\Facades\DB::statement('alter table comments alter column commentable_type set default \'book\';');
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
