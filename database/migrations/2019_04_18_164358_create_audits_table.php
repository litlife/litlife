<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuditsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('audits')) {
			Schema::create('audits', function (Blueprint $table) {
				$table->integer('id', true);
				$table->string('user_type', 30)->nullable();
				$table->bigInteger('user_id')->nullable();
				$table->string('event');
				$table->string('auditable_type', 30);
				$table->bigInteger('auditable_id');
				$table->text('old_values')->nullable();
				$table->text('new_values')->nullable();
				$table->text('url')->nullable();
				$table->string('ip_address')->nullable();
				$table->string('user_agent')->nullable();
				$table->string('tags')->nullable();
				$table->timestamps();
				$table->index(['auditable_type', 'auditable_id']);
				$table->index(['user_id', 'user_type']);
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
		Schema::drop('audits');
	}

}
