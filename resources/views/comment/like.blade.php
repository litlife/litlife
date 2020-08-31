<div class="comment_like d-inline-block">

	<button class="liked btn btn-light" type="button"
			@if (!$vote or $vote->vote < 1) style="display: none;" @endif>
		<i class="fas fa-heart" style="color:red"></i>
		<span class="counter"
			  @if (empty($item->vote_up)) style="display:none;" @endif>{{ intval($item->vote_up) }}</span>
	</button>

	<button class="empty btn btn-light" type="button"
			@if ($vote and $vote->vote > 0) style="display: none;" @endif>
		<i class="far fa-heart"></i>
		<span class="counter"
			  @if (empty($item->vote_up)) style="display:none;" @endif>{{ intval($item->vote_up) }}</span>
	</button>

</div>