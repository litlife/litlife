@isset($forumGroup->image)
	<img class="mr-2 rounded" alt="{{ $forumGroup->name }}"
		 srcset="{{ $forumGroup->image->getUrlWithImageResolution(60, 60, 90) }} 2x, {{ $forumGroup->image->getUrlWithImageResolution(90, 90, 85) }} 3x"
		 src="{{ $forumGroup->image->getUrlWithImageResolution(30, 30, 95) }}"
		 style="min-width: 30px; min-height: 30px; max-width: 30px; max-height: 30px;"/>
@endisset

