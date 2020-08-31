@if ($vote >= 7) <span class="badge badge-success">{{ $vote }}</span>
@elseif ($vote <= 3) <span class="badge badge-danger">{{ $vote }}</span>
@else <span class="badge badge-secondary">{{ $vote }}</span> @endif