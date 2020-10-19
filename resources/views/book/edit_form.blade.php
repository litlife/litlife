<form class="mb-3" role="form"
	  action="{{ route('books.attachments.store', ['book' => $book->id, 'setCover' => 'true']) }}"
	  method="post" enctype="multipart/form-data">

	@csrf

	<div class="row form-group">
		<div class="col-md-9 col-lg-10">
			{{ __('book.book_helper') }}
		</div>
	</div>

	<div class="row form-group">
		<label for="file" class="col-md-3 col-lg-2 col-form-label">{{ __('book.cover') }}:</label>
		<div class="col-md-9 col-lg-10">
			<div class="mb-3">
				<x-book-cover :book="$book" width="200" height="200" href="0" style="max-width: 100%;"/>
			</div>

			<div class="">
				<input size="{{ ByteUnits\Metric::bytes(config('litlife.max_image_size'))->numberOfBytes() }}"
					   type="file" name="file"/>
			</div>

			<small class="form-text text-muted">
				{{ __('common.max_size') }}:
				{{ ByteUnits\Metric::kilobytes(config('litlife.max_image_size'))->format() }}
			</small>

			<small class="form-text text-muted">
				{{ __('book.cover_helper') }}
			</small>

		</div>
	</div>

	<div class="row form-group">
		<div class="col-md-9 col-lg-10 offset-md-3 offset-lg-2">
			<button class="btn btn-primary" type="submit">{{ __('common.upload') }}</button>

			@can ('remove_cover', $book)
				<a href="{{ route('books.remove_cover', $book) }}"
				   class="btn btn-light">{{ __('book.remove_cover') }}</a>
			@endcan
		</div>
	</div>

</form>

<form role="form" action="{{ route('books.update', $book) }}"
	  method="post" enctype="multipart/form-data">

	@csrf
	@method('patch')

	@isset ($redirectSuccessUrl)
		<input type="hidden" name="redirect_success_url" value="{{ $redirectSuccessUrl }}">
	@endisset

	<div class="row form-group">
		<label for="title" class="col-md-3 col-lg-2 col-form-label">{{ __('book.title').' *' }}</label>
		<div class="col-md-9 col-lg-10">

                    <textarea id="title" name="title"
							  class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}"
							  aria-describedby="title_help_block">{{ old('title') ?: $book->title }}</textarea>

			<small id="title_help_block" class="form-text text-muted">
				{!! __('book.title_helper') !!}
			</small>
		</div>
	</div>

	<div class="row form-group">
		{{ Form::label('', '', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
		<div class="col-md-9 col-lg-10">
			<div class="form-check form-check-inline">

				@if ($cantEditSiLpPublishFields or $book->isForSale())
					<input name="is_si" type="hidden" @if ($book->is_si) value="1" @else value="0" @endif>
				@else
					<input name="is_si" type="hidden" value="0">
				@endif

				<input name="is_si" class="form-check-input" type="checkbox" id="is_si" value="1"
					   @if (old('is_si') ?? $book->is_si) checked="checked" @endif
					   @if ($cantEditSiLpPublishFields or $book->isForSale()) disabled="disabled" @endif>
				<label class="form-check-label" for="is_si">{{ __('book.is_si') }}</label>
			</div>

			<div class="form-check form-check-inline">

				@if ($cantEditSiLpPublishFields or $book->isForSale())
					<input name="is_lp" type="hidden" @if ($book->is_lp) value="1" @else value="0" @endif>
				@else
					<input name="is_lp" type="hidden" value="0">
				@endif

				<input name="is_lp" class="form-check-input" type="checkbox" id="is_lp" value="1"
					   @if (old('is_lp') ?? $book->is_lp) checked="checked" @endif
					   @if ($cantEditSiLpPublishFields or $book->isForSale()) disabled="disabled" @endif>
				<label class="form-check-label" for="is_lp">{{ __('book.is_lp') }}</label>
			</div>

			<div class="form-check form-check-inline">
				<input name="is_collection" type="hidden" value="0">
				<input name="is_collection" class="form-check-input" type="checkbox" id="is_collection"
					   value="1"
					   @if (old('is_collection') ?? $book->is_collection) checked="checked" @endif>
				<label class="form-check-label" for="is_collection">{{ __('book.is_collection') }}</label>
			</div>

			<div class="form-check form-check-inline">
				<input name="images_exists" type="hidden" value="0">
				<input name="images_exists" class="form-check-input" type="checkbox" id="images_exists"
					   value="1"
					   @if (old('images_exists') ?? $book->images_exists) checked="checked" @endif>
				<label class="form-check-label" for="images_exists">{{ __('book.images_exists') }}</label>
			</div>

			<small class="form-text text-muted">
				{!! __('book.other_title_helper') !!}
			</small>
		</div>
	</div>

	@include('book.edit.genres_list')

	@include('book.edit.authors_list', ['type' => 'writers'])

	<div class="row form-group">
		<label for="ti_lb" class="col-md-3 col-lg-2 col-form-label">{{ __('book.ti_lb') }} * </label>
		<div class="col-md-9 col-lg-10">
			<select id="ti_lb" name="ti_lb" class="form-control{{ $errors->has('ti_lb') ? ' is-invalid' : '' }}">

				<option></option>
				@foreach($languages as $language)

					@if ($language->code == (old('ti_lb') ?? $book->ti_lb))
						<option value="{{ $language->code }}" selected>{{ $language->name }}
							- {{ $language->code }}</option>
					@else
						<option value="{{ $language->code }}">{{ $language->name }}
							- {{ $language->code }}</option>
					@endif

				@endforeach
			</select>

			<small class="form-text text-muted">
				{{ __('book.ti_lb_helper') }}
			</small>

		</div>
	</div>

	<div class="row form-group">
		<label for="ti_olb" class="col-md-3 col-lg-2 col-form-label">{{ __('book.ti_olb') }} *</label>
		<div class="col-md-9 col-lg-10">
			<select id="ti_olb" name="ti_olb" class="form-control {{ $errors->has('ti_olb') ? ' is-invalid' : '' }}">

				<option></option>
				@foreach($languages as $language)
					@if ($language->code == (old('ti_olb') ?? $book->ti_olb))
						<option value="{{ $language->code }}" selected>{{ $language->name }}
							- {{ $language->code }}</option>
					@else
						<option value="{{ $language->code }}">{{ $language->name }}
							- {{ $language->code }}</option>
					@endif

				@endforeach
			</select>

			<small class="form-text text-muted">
				{{ __('book.ti_olb_helper') }}
			</small>

		</div>
	</div>

	@include('book.edit.authors_list', ['type' => 'translators'])

	@include('book.edit.sequences_list')

	@include('ckeditor_annotation', ['book' => $book, 'height' => '200'])

	<div class="row form-group">
		<label for="annotation" class="col-md-3 col-lg-2 col-form-label">{{ __('book.annotation') }}</label>
		<div class="col-md-9 col-lg-10">
                    <textarea id="annotation" class="ckeditor_book"
							  name="annotation">{{ old('annotation') ?: (isset($book->annotation) ? $book->annotation->getContent() : null) }}</textarea>
		</div>
	</div>

	@can ('addKeywords', $book)
		<div class="row form-group">
			<label for="keywords"
				   class="col-md-3 col-lg-2 col-form-label">{{ __('book.keywords') }}</label>
			<div class="col-md-9 col-lg-10">
				<select id="keywords" name="keywords[]"
						multiple data-placeholder="{{ __('common.enter_name_or_id') }}"
						class="form-control {{ $errors->has('keywords') ? ' is-invalid' : '' }}">
					@if (old('keywords'))
						@foreach(old('keywords') as $c => $keyword)
							<option value="{{ $keyword }}" selected>{{ $keyword }}</option>
						@endforeach
					@else
						@foreach($book->book_keywords as $c => $keyword)
							@if (!empty($keyword->keyword))
								<option value="{{ $keyword->keyword->text }}" selected>{{ $keyword->keyword->text }}</option>
							@endif
						@endforeach
					@endif
				</select>

				@can ('create', \App\Keyword::class)
					<a href="{{ route('keywords.create') }}" class="btn btn-outline-primary btn-sm mt-2" target="_blank">
						{{ __('book.create_a_new_keyword') }}
					</a>
				@endcan

				<a target="_blank" class="btn btn-outline-primary btn-sm mt-2" href="{{ route('text_block.keywords_helper') }}">
					{{ __('book.list_of_all_keywords') }}
				</a>
			</div>
		</div>
	@endcan

	<div class="row form-group{{ $errors->has('year_writing') ? ' has-error' : '' }}">
		{{ Form::label('year_writing', __('book.year_writing').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
		<div class="col-md-9 col-lg-10">
			{{ Form::text('year_writing', old('year_writing') ?: $book->year_writing, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="row form-group">
		<label for="rightholder" class="col-md-3 col-lg-2 col-form-label">{{ __('book.rightholder') }}</label>
		<div class="col-md-9 col-lg-10">
			<input id="rightholder" name="rightholder"
				   class="form-control{{ $errors->has('rightholder') ? ' is-invalid' : '' }}"
				   type="text"
				   value="{{ old('rightholder') ?: $book->rightholder }}"/>
		</div>
	</div>

	@can('editFieldOfPublicDomain', $book)

		<div class="row form-group{{ $errors->has('is_public') ? ' has-error' : '' }}">
			{{ Form::label('is_public', __('book.is_public').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
			<div class="col-md-9 col-lg-10">
				<label class="form-check form-check-inline">
					{{ Form::hidden('is_public', '0') }}
					{{ Form::checkbox('is_public', 1, old('is_public') ?: $book->is_public, ['class' => 'form-check-input']) }}
					<small class="form-text text-muted">
						{{ __('book.is_public_helper') }}
					</small>
				</label>
			</div>
		</div>

		<div class="row form-group{{ $errors->has('year_public') ? ' has-error' : '' }}">
			{{ Form::label('year_public', __('book.year_public').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
			<div class="col-md-9 col-lg-10">
				{{ Form::text('year_public', old('year_public') ?: $book->year_public, ['class' => 'form-control']) }}
			</div>
		</div>

	@endcan


	<div class="row form-group">
		<label for="pi_pub" class="col-md-3 col-lg-2 col-form-label">{{ __('book.pi_pub') }}</label>
		<div class="col-md-9 col-lg-10">
			<input id="pi_pub" name="pi_pub" class="form-control{{ $errors->has('pi_pub') ? ' is-invalid' : '' }}"
				   type="text"
				   @if ($cantEditSiLpPublishFields or $book->isForSale()) readonly="readonly" @endif
				   value="{{ old('pi_pub') ?: $book->pi_pub }}"/>
			<small class="form-text text-muted">
				{{ __('book.pi_pub_helper') }}
			</small>
		</div>
	</div>

	<div class="row form-group">
		{{ Form::label('pi_city', __('book.pi_city').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
		<div class="col-md-9 col-lg-10">
			<input id="pi_city" name="pi_city" class="form-control{{ $errors->has('pi_city') ? ' is-invalid' : '' }}"
				   type="text"
				   @if ($cantEditSiLpPublishFields or $book->isForSale()) readonly="readonly" @endif
				   value="{{ old('pi_city') ?: $book->pi_city }}"/>
			<small class="form-text text-muted">
				{{ __('book.pi_city_helper') }}
			</small>
		</div>
	</div>

	<div class="row form-group">
		{{ Form::label('pi_year', __('book.pi_year').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
		<div class="col-md-9 col-lg-10">
			<input id="pi_year" name="pi_year" class="form-control{{ $errors->has('pi_year') ? ' is-invalid' : '' }}"
				   type="text"
				   @if ($cantEditSiLpPublishFields or $book->isForSale()) readonly="readonly" @endif
				   value="{{ old('pi_year') ?: $book->pi_year }}"/>
			<small class="form-text text-muted">
				{{ __('book.pi_year_helper') }}
			</small>
		</div>
	</div>

	<div class="row form-group">
		{{ Form::label('pi_isbn', __('book.pi_isbn').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
		<div class="col-md-9 col-lg-10">
			<input id="pi_isbn" name="pi_isbn" class="form-control{{ $errors->has('pi_isbn') ? ' is-invalid' : '' }}"
				   type="text"
				   @if ($cantEditSiLpPublishFields or $book->isForSale()) readonly="readonly" @endif
				   value="{{ old('pi_isbn') ?: $book->pi_isbn }}"/>
			<small class="form-text text-muted">
				{{ __('book.pi_isbn_hepler') }}
			</small>
		</div>
	</div>

	<div class="row form-group">
		{{ Form::label('ready_status', __('book.ready_status').' *', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
		<div class="col-md-9 col-lg-10">
			<select name="ready_status"
					class="form-control form-control{{ $errors->has('ready_status') ? ' is-invalid' : '' }}">
				<option value=""> -</option>
				@foreach (\App\Enums\BookComplete::getKeys() as $item => $value)
					<option value="{{ $value }}"
							@if (in_array($value, [old('ready_status'), $book->ready_status])) selected="selected" @endif>
						{{ __('book.text_complete.'.$value) }}
					</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="row form-group">
		{{ Form::label('swear', __('book.swear').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
		<div class="col-md-9 col-lg-10">
			<select name="swear"
					class="form-control form-control{{ $errors->has('swear') ? ' is-invalid' : '' }}">
				@foreach ($book->swearArray as $item => $value)
					<option value="{{ $value }}"
							@if (in_array($value, [old('swear'), $book->swear])) selected="selected" @endif>
						{{ __('book.swear_array.'.$value) }}
					</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="row form-group{{ $errors->has('age') ? ' has-error' : '' }}">
		{{ Form::label('age', __('book.age').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
		<div class="col-md-9 col-lg-10">
			{{ Form::text('age', old('age') ?: $book->age, ['class' => 'form-control']) }}

			<small class="form-text text-muted">
				{!! __('book.age_helper') !!}
			</small>
		</div>
	</div>

	<div class="row form-group">
		<label for="editors"
			   class="col-md-3 col-lg-2 col-form-label">{{ trans_choice('book.editors', 2) }}</label>
		<div class="col-md-9 col-lg-10">
			<select id="editors" name="editors[]"
					multiple class="author-select form-control {{ $errors->has('editors') ? ' is-invalid' : '' }}"
					data-placeholder="{{ __('common.enter_name_or_id') }}">
				@foreach(old('editors') ? App\Author::find(old('editors')) : $book->editors as $c => $author)
					<option value="{{ $author->id }}" selected>{{ $author->fullName }}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="row form-group">
		<label for="compilers"
			   class="col-md-3 col-lg-2 col-form-label">{{ trans_choice('book.compilers', 2) }}</label>
		<div class="col-md-9 col-lg-10">
			<select id="compilers" name="compilers[]"
					multiple
					class="author-select form-control {{ $errors->has('compilers') ? ' is-invalid' : '' }}"
					data-placeholder="{{ __('common.enter_name_or_id') }}">
				@foreach(old('compilers') ? App\Author::find(old('compilers')) : $book->compilers as $c => $author)
					<option value="{{ $author->id }}" selected>{{ $author->fullName }}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="row form-group">
		<label for="illustrators"
			   class="col-md-3 col-lg-2 col-form-label">{{ trans_choice('book.illustrators', 2) }}</label>
		<div class="col-md-9 col-lg-10">
			<select id="illustrators" name="illustrators[]"
					multiple
					class="author-select form-control {{ $errors->has('illustrators') ? ' is-invalid' : '' }}"
					data-placeholder="{{ __('common.enter_name_or_id') }}">
				@foreach(old('illustrators') ? App\Author::find(old('illustrators')) : $book->illustrators as $c => $author)
					<option value="{{ $author->id }}" selected>{{ $author->fullName }}</option>
				@endforeach
			</select>

			<small class="form-text text-muted">
				{{ __('book.illustrators_helper') }}
			</small>
		</div>
	</div>

	<div class="row form-group">
		<label for="copy_protection"
			   class="col-md-3 col-lg-2 col-form-label">{{ __('book.copy_protection') }}</label>
		<div class="col-md-9 col-lg-10">
			<label class="form-check">
				<input name="copy_protection" type="hidden" value="0">
				<input name="copy_protection" class="form-check-input" type="checkbox" id="copy_protection" value="1"
					   @if (old('copy_protection') ?? $book->copy_protection) checked="checked" @endif>

				<label class="form-check-label" for="copy_protection">
					{{ __('Check to enable copy protection for online reading text.') }} <br/>
					{{ __('Keep in mind that this function does not affect the book files.') }}
				</label>
			</label>
		</div>
	</div>

	<div class="row form-group">
		<div class="col-md-9 col-lg-10 offset-md-3 offset-lg-2">
			<button type="submit" class="btn btn-primary">
				{{ __('common.save') }}
			</button>
		</div>
	</div>

</form>
