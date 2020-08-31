<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateManagersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('managers')) {
			Schema::create('managers', function (Blueprint $table) {
				$table->integer('id', true);
				$table->bigInteger('create_user_id')->default(0);
				$table->bigInteger('user_id')->default(0)->index();
				$table->string('character');
				$table->bigInteger('manageable_id')->default(0);
				$table->integer('add_time')->default(0);
				$table->smallInteger('hide')->default(0);
				$table->integer('hide_time')->default(0);
				$table->integer('hide_user')->default(0);
				$table->text('comment')->nullable();
				$table->timestamps();
				$table->softDeletes();
				$table->dateTime('accepted_at')->nullable();
				$table->integer('check_user_id')->nullable();
				$table->string('manageable_type', 30)->default('author');
				$table->dateTime('rejected_at')->nullable();
				$table->dateTime('sent_for_review_at')->nullable();
				$table->smallInteger('status')->nullable();
				$table->dateTime('status_changed_at')->nullable();
				$table->integer('status_changed_user_id')->nullable();
				$table->index(['manageable_id', 'manageable_type']);
				$table->unique(['manageable_type', 'manageable_id', 'user_id', 'deleted_at'], 'managers_manageable_type_manageable_id_user_id_deleted_at_uniqu');
				$table->index(['status', 'status_changed_at']);
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
		Schema::drop('managers');
	}

}
