<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterComplaintsTableRemoveUniqueIndex extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Illuminate\Support\Facades\DB::statement('alter table complaints
drop constraint if exists complaints_complainable_type_complainable_id_user_id_unique;');

		\Illuminate\Support\Facades\DB::statement('alter table complaints
drop constraint if exists complaints_complainable_type_complainable_id_create_user_id_uni;');

		Schema::table('complaints', function (Blueprint $table) {
			$table->index(['complainable_type', 'complainable_id', 'create_user_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('complaints', function (Blueprint $table) {
			$table->unique(['complainable_type', 'complainable_id', 'create_user_id']);
		});

		Schema::table('complaints', function (Blueprint $table) {
			$table->dropIndex('complaints_complainable_type_complainable_id_create_user_id_ind');
		});
	}
}
