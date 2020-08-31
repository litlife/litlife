<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBooksAverageRatingForPeriodAddRatingColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('books_average_rating_for_period', function (Blueprint $table) {
			$table->integer('all_rating')->default(0);
		});

		\Illuminate\Support\Facades\DB::statement('drop index if exists books_average_rating_for_period_day_rating_desc_index;');
		\Illuminate\Support\Facades\DB::statement('drop index if exists books_average_rating_for_period_month_rating_desc_index;');
		\Illuminate\Support\Facades\DB::statement('drop index if exists books_average_rating_for_period_quarter_rating_desc_index;');
		\Illuminate\Support\Facades\DB::statement('drop index if exists books_average_rating_for_period_week_rating_desc_index;');
		\Illuminate\Support\Facades\DB::statement('drop index if exists books_average_rating_for_period_year_rating_desc_index;');

		\Illuminate\Support\Facades\DB::statement('create index books_average_rating_for_period_day_rating_all_rating_index
	on books_average_rating_for_period (day_rating desc, all_rating desc);');

		\Illuminate\Support\Facades\DB::statement('create index books_average_rating_for_period_week_rating_all_rating_index
	on books_average_rating_for_period (week_rating desc, all_rating desc);');

		\Illuminate\Support\Facades\DB::statement('create index books_average_rating_for_period_quarter_rating_all_rating_index
	on books_average_rating_for_period (quarter_rating desc, all_rating desc);');

		\Illuminate\Support\Facades\DB::statement('create index books_average_rating_for_period_month_rating_all_rating_index
	on books_average_rating_for_period (month_rating desc, all_rating desc);');

		\Illuminate\Support\Facades\DB::statement('create index books_average_rating_for_period_year_rating_all_rating_index
	on books_average_rating_for_period (year_rating desc, all_rating desc);');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('books_average_rating_for_period', function (Blueprint $table) {
			$table->dropColumn('all_rating');
		});

		\Illuminate\Support\Facades\DB::statement('drop index if exists books_average_rating_for_period_day_rating_all_rating_index;');
		\Illuminate\Support\Facades\DB::statement('drop index if exists books_average_rating_for_period_week_rating_all_rating_index;');
		\Illuminate\Support\Facades\DB::statement('drop index if exists books_average_rating_for_period_quarter_rating_all_rating_index;');
		\Illuminate\Support\Facades\DB::statement('drop index if exists books_average_rating_for_period_month_rating_all_rating_index;');
		\Illuminate\Support\Facades\DB::statement('drop index if exists books_average_rating_for_period_year_rating_all_rating_index;');
	}
}
