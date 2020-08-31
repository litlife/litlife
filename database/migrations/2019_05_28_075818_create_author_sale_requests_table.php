<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorSaleRequestsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('author_sale_requests', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('create_user_id');
			$table->integer('manager_id');
			$table->integer('author_id');
			$table->text('text');
			$table->text('review_comment')->nullable();
			$table->smallInteger('status')->nullable();
			$table->dateTime('status_changed_at')->nullable();
			$table->integer('status_changed_user_id')->nullable();
			$table->timestamps();
			$table->softDeletes();


		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('author_sale_requests');
	}
}
