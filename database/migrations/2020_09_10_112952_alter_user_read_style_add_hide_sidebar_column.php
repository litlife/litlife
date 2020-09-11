<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserReadStyleAddHideSidebarColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_read_styles', function (Blueprint $table) {
			$table->boolean('show_sidebar')->default(false)->comment(__('user.read_style_array.show_sidebar'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_read_styles', function (Blueprint $table) {
			$table->dropColumn('show_sidebar');
		});
	}
}
