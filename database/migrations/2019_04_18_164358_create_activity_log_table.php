<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivityLogTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('activity_log')) {
			Schema::create('activity_log', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->string('description');
				$table->bigInteger('subject_id')->default(0);
				$table->string('subject_type', 30);
				$table->integer('causer_id')->default(0);
				$table->integer('time')->default(0);
				$table->text('text')->nullable();
				$table->timestamps();
				$table->string('causer_type', 30)->nullable();
				$table->string('log_name')->nullable();
				$table->text('properties')->nullable();
				$table->index(['causer_id', 'causer_type']);
				$table->index(['subject_id', 'subject_type']);
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
		Schema::drop('activity_log');
	}

}
