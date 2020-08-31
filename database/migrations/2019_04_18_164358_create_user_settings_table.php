<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserSettingsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_settings')) {
			Schema::create('user_settings', function (Blueprint $table) {
				$table->bigInteger('user_id')->default(0)->unique();
				$table->text('bookmark_folder_order')->nullable()->default('');
				$table->text('email_delivery')->nullable()->default('');
				$table->text('user_access')->nullable()->default('');
				$table->text('genre_blacklist')->nullable()->default('');
				$table->integer('blog_top_record')->nullable();
				$table->timestamps();
				$table->text('permissions_to_act')->nullable();
				$table->boolean('login_with_id')->default(0)->comment('Можно ли использовать в качестве логина id');
				$table->integer('font_size_px')->default(16);
				$table->smallInteger('font_family')->nullable();
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
		Schema::drop('user_settings');
	}

}
