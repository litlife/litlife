<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserDatasInvitationToTakeSurveyHasBeenSent extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_datas', function (Blueprint $table) {
			$table->boolean('invitation_to_take_survey_has_been_sent')->default(false);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_datas', function (Blueprint $table) {
			$table->dropColumn('invitation_to_take_survey_has_been_sent');
		});
	}
}
