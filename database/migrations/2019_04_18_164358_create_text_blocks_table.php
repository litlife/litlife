<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTextBlocksTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('text_blocks')) {
			Schema::create('text_blocks', function (Blueprint $table) {
				$table->string('name')->unique();
				$table->text('text');
				$table->bigInteger('user_id');
				$table->integer('time')->nullable();
				$table->smallInteger('show_for_all')->default(0);
				$table->timestamps();
				$table->integer('id', true);
				$table->dateTime('user_edited_at')->nullable();
			});
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('text_blocks');
	}

}
