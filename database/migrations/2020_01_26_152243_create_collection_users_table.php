<?php

use App\Collection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('collection_users', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('collection_id')->comment(__('collection_user.collection_id'));
			$table->integer('user_id')->comment(__('collection_user.user_id'));
			$table->integer('create_user_id')->comment(__('collection_user.user_id'));
			$table->string('description', 100)->nullable()->comment(__('collection_user.description'));
			$table->boolean('can_user_manage')->default(false)->comment(__('collection_user.can_user_manage'));
			$table->boolean('can_edit')->default(false)->comment(__('collection_user.can_edit'));
			$table->boolean('can_add_books')->default(false)->comment(__('collection_user.can_add_books'));
			$table->boolean('can_remove_books')->default(false)->comment(__('collection_user.can_remove_books'));
			$table->boolean('can_edit_books_description')->default(false)->comment(__('collection_user.can_edit_books_description'));
			$table->boolean('can_comment')->default(false)->comment(__('collection_user.can_comment'));
			$table->softDeletes();
			$table->timestamps();
		});

		Schema::table('collections', function (Blueprint $table) {
			$table->smallInteger('users_count')->nullable()->comment(__('collection.users_count'));
		});

		Collection::chunkById(10, function ($collections) {
			foreach ($collections as $collection) {
				$collection->refreshUsersCount();
				$collection->save();
			}
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('collection_users');

		Schema::table('collections', function (Blueprint $table) {
			$table->dropColumn('users_count');
		});
	}
}
