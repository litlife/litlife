@extends('layouts.app')

@section('content')

	<div class="container">
		<div class="row">
			<div class="col-md-8">

				<div class="panel panel-default">

					<div class="panel-body">

						@if ((count($top_comments) > 0) and ($comments->currentPage() < 2))

							<div class="container" style="width:500px">
								<ul style="list-style-type:none">
									@foreach ($top_comments as $comment)
										@include("comment/item", $comment)
									@endforeach
								</ul>
							</div>

						@endif

						@can('create', new App\Comment())

							@include('ckeditor')

							{!!  Form::open(['action' => array('CommentController@store', $book->id) ]) !!}

							<div class="form-group">

                                <textarea id="ckeditor" class="form-control" rows="5"
										  name="text">{{ old('text') ?? ''  }}</textarea>
								@if ($errors->has('text')) <p class="help-block">{{ $errors->first('text') }}</p> @endif

							</div>

							<div style=" margin-top: 5px">
								<button type="submit" class="btn btn-light">{{ __('common.send') }}</button>
							</div>

							{!! Form::close() !!}

						@endcan

						@if(count($comments) > 0)

							{{ $comments->appends(request()->except(['page', 'ajax']))->links() }}

							<div class="container" style="width:500px">
								<ul style="list-style-type:none">
									@foreach ($comments as $comment)
										@include("comment/item", $comment);
									@endforeach
								</ul>
							</div>

							{{ $comments->appends(request()->except(['page', 'ajax']))->links() }}

						@else
							<div class="alert alert-info">
								{{ __('comment.nothing_found') }}
							</div>
						@endif

					</div>
				</div>
			</div>
		</div>
	</div>
@endsection