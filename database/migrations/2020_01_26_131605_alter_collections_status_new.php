<?php

use App\Enums\StatusEnum;
use App\Enums\UserAccountPermissionValues;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCollectionsStatusNew extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('collections', function (Blueprint $table) {
			$table->tinyInteger('status')->index()->nullable()->comment(__('collection.status'));
			$table->timestamp('status_changed_at')->nullable()->comment(__('collection.status_changed_at'));
			$table->integer('status_changed_user_id')->nullable()->comment(__('collection.status_changed_user_id'));
			$table->smallInteger('who_can_see')->nullable()->change();
		});

		\Illuminate\Support\Facades\DB::table('collections')
			->where('who_can_see', UserAccountPermissionValues::me)
			->update(['status' => StatusEnum::Private]);

		\Illuminate\Support\Facades\DB::table('collections')
			->where('who_can_see', UserAccountPermissionValues::everyone)
			->update(['status' => StatusEnum::Accepted]);

		Schema::table('collections', function (Blueprint $table) {
			$table->dropColumn('who_can_see');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('collections', function (Blueprint $table) {
			if (!Schema::hasColumn('collections', 'who_can_see'))
				$table->smallInteger('who_can_see')->nullable();
		});

		\Illuminate\Support\Facades\DB::table('collections')
			->where('status', StatusEnum::Private)
			->update(['who_can_see' => UserAccountPermissionValues::me]);

		\Illuminate\Support\Facades\DB::table('collections')
			->where('status', StatusEnum::Accepted)
			->update(['who_can_see' => UserAccountPermissionValues::everyone]);

		Schema::table('collections', function (Blueprint $table) {
			$table->dropColumn('status');
			$table->dropColumn('status_changed_at');
			$table->dropColumn('status_changed_user_id');
		});
	}
}
