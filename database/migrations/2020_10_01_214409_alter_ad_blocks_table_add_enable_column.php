<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAdBlocksTableAddEnableColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ad_blocks', function (Blueprint $table) {
			$table->boolean('enabled')->default(true)->comment(__('ad_block.enable'));
			$table->string('description')->nullable()->comment(__('ad_block.description'));
			$table->dropUnique('ad_blocks_name_unique');
			$table->index('name');
			$table->timestamp('user_updated_at')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ad_blocks', function (Blueprint $table) {
			$table->dropColumn('enabled');
			$table->dropIndex('ad_blocks_name_index');
			$table->unique('name');
			$table->dropColumn('description');
			$table->dropColumn('user_updated_at');
		});
	}
}
