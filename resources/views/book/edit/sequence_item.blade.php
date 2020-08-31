<li class="item list-group-item">

	<div class="btn btn-light">
		<span class="move fas fa-arrows-alt-v"></span>
	</div>
	<div class="btn btn-light">
		<span class="delete far fa-trash-alt"></span>
	</div>

	<a href="{{ route('sequences.show', $sequence) }}">{{ $sequence->name }}</a> ID: {{ $sequence->id }}
	<input name="sequences[{{ $sequence->id }}][id]" type="hidden" value="{{ $sequence->id }}"/>

	<div style="display:inline-block">
		<input name="sequences[{{ $sequence->id }}][number]" type="text" class="form-control"
			   value="{{ old('sequences.'.$sequence->id.'.number') ?? @$sequence->pivot->number }}" style="width:50px"/>
	</div>

</li>