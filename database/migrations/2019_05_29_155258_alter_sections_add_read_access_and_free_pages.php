<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSectionsAddReadAccessAndFreePages extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sections', function (Blueprint $table) {
			$table->smallInteger('status')->default(0)->comment('Статус главы. Пока будут варианты опубликована и в личном доступе или черновик');
			$table->dateTime('status_changed_at')->nullable()->comment('Дата изменения статуса');
			$table->integer('status_changed_user_id')->nullable()->comment('Пользователь последний изменивший статус');

			$table->index(['book_id', 'type', 'status']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sections', function (Blueprint $table) {
			$table->dropColumn('status');
			$table->dropColumn('status_changed_at');
			$table->dropColumn('status_changed_user_id');
		});
	}
}
