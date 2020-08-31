@foreach ($managers as $manager)
	@if ($manager->manageable->books->where('is_lp', false)->count() > 0 and !empty(optional($manager->user)->notice_email))
		{{ optional(optional($manager->user)->notice_email)->email }}
		{{ $manager->manageable->rating }} {{ $manager->manageable->name }}
		<br/>
	@endif
@endforeach

