<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateKeywordsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('keywords')) {
			Schema::create('keywords', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->string('text')->index('keywords_text_trgm_idx');
				$table->smallInteger('count')->default(0);
				$table->smallInteger('action')->default(0);
				$table->smallInteger('hide')->default(0);
				$table->integer('hide_time')->nullable();
				$table->integer('hide_user')->nullable();
				$table->integer('create_user_id')->nullable();
				$table->timestamps();
				$table->softDeletes();
				$table->dateTime('accepted_at')->nullable();
				$table->dateTime('sent_for_review_at')->nullable();
				$table->dateTime('rejected_at')->nullable();
				$table->smallInteger('status')->nullable();
				$table->dateTime('status_changed_at')->nullable();
				$table->integer('status_changed_user_id')->nullable();
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
		Schema::drop('keywords');
	}

}
