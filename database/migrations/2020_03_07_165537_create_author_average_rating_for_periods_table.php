<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorAverageRatingForPeriodsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('author_average_rating_for_periods', function (Blueprint $table) {
			$table->integer('author_id')->unique();
			$table->integer('day_rating')->nullable();
			$table->integer('week_rating')->nullable();
			$table->integer('month_rating')->nullable();
			$table->integer('quarter_rating')->nullable();
			$table->integer('year_rating')->nullable();
			$table->integer('all_rating')->nullable();
		});

		\Illuminate\Support\Facades\DB::statement('create index author_average_rating_for_periods_day_rating_all_rating_index
	on author_average_rating_for_periods (day_rating desc nulls last, all_rating desc nulls last);');

		\Illuminate\Support\Facades\DB::statement('create index author_average_rating_for_periods_week_rating_all_rating_index
	on author_average_rating_for_periods (week_rating desc nulls last, all_rating desc nulls last);');

		\Illuminate\Support\Facades\DB::statement('create index author_average_rating_for_periods_month_rating_all_rating_index
	on author_average_rating_for_periods (month_rating desc nulls last, all_rating desc nulls last);');

		\Illuminate\Support\Facades\DB::statement('create index author_average_rating_for_periods_quarter_rating_all_rating_index
	on author_average_rating_for_periods (quarter_rating desc nulls last, all_rating desc nulls last);');

		\Illuminate\Support\Facades\DB::statement('create index author_average_rating_for_periods_year_rating_all_rating_index
	on author_average_rating_for_periods (year_rating desc nulls last, all_rating desc nulls last);');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('author_average_rating_for_periods');
	}
}
