<ul class="list-group list-group-horizontal" style="text-align: center;">
	@foreach ($likes as $like)
		<li title="{{ optional($like->create_user)->userName ?? __('user.deleted') }}" class="list-group-item p-1">
			<div style="max-width:30px; max-height:30px;">
				<x-user-avatar :user="$like->create_user" width="30" height="30"/>
			</div>
		</li>
	@endforeach
</ul>




