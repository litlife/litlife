<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
	public function index()
	{
		$activityLogs = Activity::with('causer')
			->latest()
			->simplePaginate();

		$activityLogs->load(['subject' => function ($query) {
			$query->any();
		}]);

		return view('activity_log.index', compact('activityLogs'));
	}
}
