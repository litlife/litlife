@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/sequences.show.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	<div class="card">
		<div class="card-body">
			<div class="sequence">

				<div class="row">

					<div class="col-12">

						<h4> {{ __('sequence.access_denied') }}</h4>

					</div>
				</div>

				<div class="row">
					<div class="col-12">

						@include('like.item', ['item' => $sequence, 'like' => pos($sequence->likes) ?: null, 'likeable_type' => 'sequence', 'likeable_id' => $sequence->id])
						@include('user_library_button', ['item' => $sequence,
						'user_library' => pos($sequence->library_users) ?: null, 'type' => 'sequence',
						'id' => $sequence->id, 'count' => $sequence->added_to_favorites_count])

					</div>
				</div>
			</div>
		</div>
	</div>

@endsection
