<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLikesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('likes')) {
			Schema::create('likes', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->string('likeable_type', 30);
				$table->bigInteger('likeable_id');
				$table->bigInteger('create_user_id');
				$table->integer('time')->default(0);
				$table->string('ip');
				$table->timestamps();
				$table->softDeletes();
				$table->index(['likeable_id', 'likeable_type', 'created_at']);
				$table->index(['likeable_id', 'likeable_type']);
				$table->unique(['likeable_id', 'likeable_type', 'create_user_id']);
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
		Schema::drop('likes');
	}

}
