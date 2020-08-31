<?php

namespace App\Observers;

use App\Genre;
use Illuminate\Support\Facades\Cache;

class GenreObserver
{
	public function creating(Genre $genre)
	{

	}

	public function created(Genre $genre)
	{
		Cache::forever('genres_count_refresh', 'true');
	}

	public function deleted(Genre $genre)
	{
		Cache::forever('genres_count_refresh', 'true');
	}

	public function restored(Genre $genre)
	{
		Cache::forever('genres_count_refresh', 'true');
	}

}