@extends('layouts.app')

@section('content')

	<div class="card">
		<div class="card-body">
			{{ trans_choice('search.minimum_length_of_the_search_string', config('litlife.minimum_number_of_letters_and_numbers'), ['characters_count' => config('litlife.minimum_number_of_letters_and_numbers')]) }}
		</div>
	</div>

@endsection