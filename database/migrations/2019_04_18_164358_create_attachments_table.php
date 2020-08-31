<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttachmentsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('attachments')) {
			Schema::create('attachments', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->bigInteger('book_id')->index();
				$table->text('name');
				$table->text('content_type');
				$table->integer('size');
				$table->string('type')->default('image');
				$table->text('parameters')->nullable();
				$table->timestamps();
				$table->softDeletes();
				$table->string('storage', 30)->default('old');
				$table->string('dirname')->nullable();
				$table->integer('create_user_id')->nullable();
				$table->string('sha256_hash')->nullable()->index();
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
		Schema::drop('attachments');
	}

}
