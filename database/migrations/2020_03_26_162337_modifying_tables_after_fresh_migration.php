<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyingTablesAfterFreshMigration extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_groups', function (Blueprint $table) {
			$table->string('key')->nullable()->comment(__('user_group.key'));
		});

		\App\UserGroup::where('name', 'ilike', 'Пользователь')
			->update(['key' => \App\Enums\UserGroupEnum::User]);

		\App\UserGroup::where('name', 'ilike', 'Администратор')
			->update(['key' => \App\Enums\UserGroupEnum::Administrator]);

		\App\UserGroup::where('name', 'ilike', 'Забаненный')
			->update(['key' => \App\Enums\UserGroupEnum::Banned]);

		\App\UserGroup::where('name', 'ilike', 'Автор')
			->update(['key' => \App\Enums\UserGroupEnum::Author]);

		\App\UserGroup::where('name', 'ilike', 'Активный комментатор')
			->update(['key' => \App\Enums\UserGroupEnum::ActiveCommentator]);

		\App\UserGroup::where('name', 'ilike', 'Мастер комментария')
			->update(['key' => \App\Enums\UserGroupEnum::CommentMaster]);

		Schema::table('user_email_tokens', function (Blueprint $table) {
			$table->bigIncrements('id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}
}
