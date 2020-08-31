<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdminNotesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('admin_notes')) {
			Schema::create('admin_notes', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->string('admin_noteable_type', 10);
				$table->bigInteger('admin_noteable_id');
				$table->text('text')->nullable();
				$table->bigInteger('create_user_id')->nullable();
				$table->integer('time')->nullable();
				$table->timestamps();
				$table->softDeletes();
				$table->dateTime('user_edited_at')->nullable();
				$table->index(['admin_noteable_id', 'admin_noteable_type']);
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
		Schema::drop('admin_notes');
	}

}
