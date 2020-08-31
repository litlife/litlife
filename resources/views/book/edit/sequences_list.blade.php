<div class="row form-group">
	<label for="sequences" class="col-md-3 col-lg-2 col-form-label">{{ trans_choice('book.sequences', 2) }}:</label>
	<div class="col-md-9 col-lg-10">

		<ol class="sequences-list sortable list-group list-group-flush">
			@if($book->sequences->count())
				@foreach(old('sequences') ? App\Sequence::whereIn('id', collect(old('sequences'))->pluck('id')->toArray())->orderByField('id', collect(old('sequences'))->pluck('id')->toArray())->get() : $book->sequences as $c => $sequence)
					@include('book.edit.sequence_item', ['sequence' => $sequence])
				@endforeach
			@endif
		</ol>

		<div class="sequence-select row">
			<div class="col-sm-6">
				<select class="form-control"></select>
			</div>
			<div class="col-sm-6">
				<a href="javascript:void(0)" class="add btn btn-outline-primary">
					{{ __('common.attach') }}
				</a>
			</div>
		</div>

		<small class="form-text text-muted">
			{{ __('book.sequences_helper') }}
		</small>

		<a class="btn btn-outline-secondary" href="{{ route('sequences.create') }}" target="_blank"
		   style="margin-top:5px;">
			{{ __('sequence.create') }}
		</a>

	</div>
</div>