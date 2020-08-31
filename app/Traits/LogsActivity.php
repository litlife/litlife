<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\ActivitylogServiceProvider;

trait LogsActivity
{
	use \Spatie\Activitylog\Traits\LogsActivity;

	public function latestActivitiesItemDeleted(): MorphMany
	{
		return $this->morphMany(ActivitylogServiceProvider::determineActivityModel(), 'subject')
			->where('description', 'deleted')
			->latest();
	}
}