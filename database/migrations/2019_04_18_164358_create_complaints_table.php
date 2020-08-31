<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateComplaintsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('complaints')) {
			Schema::create('complaints', function (Blueprint $table) {
				$table->integer('id', true);
				$table->string('complainable_type', 30);
				$table->integer('complainable_id');
				$table->integer('create_user_id');
				$table->text('text');
				$table->timestamps();
				$table->softDeletes();
				$table->dateTime('accepted_at')->nullable();
				$table->dateTime('sent_for_review_at')->nullable();
				$table->dateTime('rejected_at')->nullable();
				$table->smallInteger('status')->nullable();
				$table->dateTime('status_changed_at')->nullable();
				$table->integer('status_changed_user_id')->nullable();
				$table->unique(['complainable_type', 'complainable_id', 'create_user_id'], 'complaints_complainable_type_complainable_id_user_id_unique');
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
		Schema::drop('complaints');
	}

}
