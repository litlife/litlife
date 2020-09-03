@extends('book.show.layout')

@section('cover')
	<div class="row mb-3">
		<div class="col-12 text-center">
			<x-book-cover :book="$book" width="200" height="400" style="max-width: 100%;"/>
		</div>
	</div>
@endsection
@section('top')@endsection
@section('grouped_books')@endsection
@section('similar')@endsection
@section('description')@endsection
@section('vote')@endsection
@section('read_buttons')@endsection
@section('admin_note')@endsection
@section('annotation')@endsection
@section('keywords')@endsection
@section('comments')@endsection
@section('buttons')
	<div class="row">
		<div class="col-12 btn-margin-bottom-1 mb-3">

			@include('like.item', [
	'item' => $book,
	'like' => @$auth_user_like,
	'likeable_type' => 'book'])

			@include('user_library_button', [
			'item' => $book,
			'user_library' => @$auth_user_library,
			'type' => 'book', 'count' => $book->added_to_favorites_count])

			<select class="read-status inline custom-select" style="width:200px;">
				@foreach (\App\Enums\ReadStatus::getValues() as $status)
					<option value="{{ $status }}"
							@if ((isset($user_read_status->status)) && ($user_read_status->status == $status)) selected @endif>
						{{ trans_choice('book.read_status_array.'.$status, 1) }}
					</option>
				@endforeach
			</select>

			@if (isset($user_book_vote))
				<a class="btn btn-light"
				   href="{{ route('books.votes.delete', $book) }}">{{ __('book_vote.delete') }}</a>
			@endif

		</div>
	</div>
@endsection
