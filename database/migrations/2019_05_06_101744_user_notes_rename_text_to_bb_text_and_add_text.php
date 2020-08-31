<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserNotesRenameTextToBbTextAndAddText extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_notes', function (Blueprint $table) {
			$table->text('bb_text')->nullable();
			$table->boolean('external_images_downloaded')->default(false);
		});

		\Illuminate\Support\Facades\DB::table('user_notes')
			->update(['bb_text' => \Illuminate\Support\Facades\DB::raw('text')]);

		Schema::table('user_notes', function (Blueprint $table) {
			$table->text('bb_text')->nullable(false)->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_notes', function (Blueprint $table) {
			$table->dropColumn('bb_text');
			$table->dropColumn('external_images_downloaded');
		});
	}
}
