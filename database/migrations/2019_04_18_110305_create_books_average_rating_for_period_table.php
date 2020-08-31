<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksAverageRatingForPeriodTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('books_average_rating_for_period')) {
			Schema::create('books_average_rating_for_period', function (Blueprint $table) {
				$table->integer('book_id')->primary();
				$table->double('day_vote_average')->default(0);
				$table->integer('day_votes_count')->default(0);
				$table->integer('day_rating')->default(0);
				$table->double('week_vote_average')->default(0);
				$table->integer('week_votes_count')->default(0);
				$table->integer('week_rating')->default(0);
				$table->double('month_vote_average')->default(0);
				$table->integer('month_votes_count')->default(0);
				$table->integer('month_rating')->default(0);
				$table->double('quarter_vote_average')->default(0);
				$table->integer('quarter_votes_count')->default(0);
				$table->integer('quarter_rating')->default(0);
				$table->double('year_vote_average')->default(0);
				$table->integer('year_votes_count')->default(0);
				$table->integer('year_rating')->default(0);
			});

			\Illuminate\Support\Facades\DB::statement(
				'create index if not exists books_average_rating_for_period_day_rating_desc_index on books_average_rating_for_period (day_rating desc);');
			\Illuminate\Support\Facades\DB::statement(
				'create index if not exists books_average_rating_for_period_week_rating_desc_index on books_average_rating_for_period (week_rating desc);');
			\Illuminate\Support\Facades\DB::statement(
				'create index if not exists books_average_rating_for_period_month_rating_desc_index on books_average_rating_for_period (month_rating desc);');
			\Illuminate\Support\Facades\DB::statement(
				'create index if not exists books_average_rating_for_period_quarter_rating_desc_index on books_average_rating_for_period (quarter_rating desc);');
			\Illuminate\Support\Facades\DB::statement(
				'create index if not exists books_average_rating_for_period_year_rating_desc_index on books_average_rating_for_period (year_rating desc);');
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('books_average_rating_for_period');
	}
}
