<a class="btn btn-sm btn-light text-muted achievement-badge" data-user-achievement-id="{{ $user_achievement->id }}"
   href="{{ route('achievements.show', ['achievement' => $user_achievement->achievement]) }}"
   data-toggle="tooltip" data-placement="top" title="{{ $user_achievement->achievement->title }}"
   style="text-decoration: none;">
	<img class="rounded"
		 srcset="{{ $user_achievement->achievement->image->fullUrlMaxSize(40, 40, 90) }} 2x, {{ $user_achievement->achievement->image->fullUrlMaxSize(60, 60, 85) }} 3x"
		 src="{{ $user_achievement->achievement->image->fullUrlMaxSize(20, 20, 95) }}"
		 style="max-width:20px; max-height:20px"/>
</a>