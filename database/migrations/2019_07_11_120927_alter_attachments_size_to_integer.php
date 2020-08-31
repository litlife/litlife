<?php

use Illuminate\Database\Migrations\Migration;

class AlterAttachmentsSizeToInteger extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		/*
		Schema::table('attachments', function (Blueprint $table) {

			$type = \Illuminate\Support\Facades\DB::connection()
				->getDoctrineColumn('attachments', 'size')
				->getType()
				->getName();

			if ($type != 'integer') {
				\Illuminate\Support\Facades\DB::statement('alter table attachments alter column size type integer using size::integer;');
			}
		});
		*/
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
