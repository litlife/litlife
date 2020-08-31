<div class="books-search-container row">

	<div class="col-lg-4 col-md-5 order-md-2 order-sm-1">
		<div class="card mb-3">
			<div class="card-body">
				<form id="collapse-book-form" class="book-form" role="form" action="{{ Request::url() }}"
					  method="get">

					<div class="form-group d-flex flex-row">
						<input name="search" class="form-control" type="text"
							   placeholder="{{ __('book.search_str') }}"
							   value="{{ $input['search'] ?? ''  }}">
						<a href="javascript:void(0);" class="btn btn-light ml-1" data-container="body"
						   data-toggle="popover" data-placement="top" data-html="true"
						   data-content="{{ __('book.search_helper') }}">
							<i class="fas fa-question"></i>
						</a>
					</div>

					<button class="btn btn-outline-primary d-sm-none btn-sm" type="button" data-toggle="collapse"
							data-target="#more_filters"
							aria-expanded="false"
							aria-controls="more_filters">
						{{ __('common.show_all_search_filters') }}
					</button>

					<div id="more_filters" class="collapse dont-collapse-xs mt-3">

						<div class="form-group">
							<div class="mb-3">
								<select name="genre[]" data-placeholder="{{ trans_choice('genre.genres', 2) }}"
										class="genres form-control select2-multiple select2-hidden-accessible" multiple
										@if ($resource->ifDisabledFilter('genres')) disabled="disabled" @endif
										style="width: 100%">
									@if (isset($genres))
										@foreach ($genres as $genre)
											<option value="{{ $genre->id }}" selected="selected">{{ $genre->name }}</option>
										@endforeach
									@endif
								</select>
							</div>

							@if (!$resource->ifDisabledFilter('genres'))

								<div class="d-flex flex-row">
									<button id="selected_genres_button" type="button"
											class="btn btn-sm btn-outline-primary text-nowrap text-truncate btn-block"
											data-toggle="modal" data-target="#selected_genres_modal">
										{{ __('genre.select_from_table') }}
									</button>

									<a class="btn btn-light flex-shrink-1 ml-2"
									   data-toggle="collapse" href="#anotherFieldForChoiceOfGenres" role="button"
									   @if (!empty($and_genres))
									   aria-expanded="true"
									   @else
									   aria-expanded="false"
									   @endif
									   aria-controls="anotherFieldForChoiceOfGenres">
										<i class="fas fa-plus"></i>
									</a>

								</div>

								@push('body_append')
									<div class="modal" id="selected_genres_modal" tabindex="-1" role="dialog"
										 aria-labelledby="exampleModalLabel"
										 aria-hidden="true">
										<div class="modal-dialog modal-lg" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title" id="exampleModalLabel">{{ __('common.select_genres') }}</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<div class="modal-body">
													<div class="container-fluid">

													</div>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-secondary"
															data-dismiss="modal">{{ __('common.close') }}</button>
												</div>
											</div>
										</div>
									</div>
								@endpush

							@endif
						</div>

						<div class="form-group collapse @if (!empty($and_genres)) show @endif" id="anotherFieldForChoiceOfGenres">
							<div class="mb-3 d-flex flex-row">
								<div class="w-100 overflow-hidden">
									<select name="and_genres[]" data-placeholder="{{ __('book.and_genre_placeholder') }}"
											class="and_genres form-control select2-multiple select2-hidden-accessible" multiple
											@if ($resource->ifDisabledFilter('genres')) disabled="disabled" @endif
											style="width: 100%">
										@if (isset($and_genres))
											@foreach ($and_genres as $genre)
												<option value="{{ $genre->id }}" selected="selected">{{ $genre->name }}</option>
											@endforeach
										@endif
									</select>
								</div>
								<div class="flex-shrink-1">
									<a href="javascript:void(0);" class="btn btn-light ml-2" data-container="body"
									   data-toggle="popover" data-placement="top" data-html="true"
									   data-content="{{ __('book.and_genre_helper') }}">
										<i class="fas fa-question"></i>
									</a>
								</div>
							</div>

							@if (!$resource->ifDisabledFilter('genres'))

								<button id="and_selected_genres_button" type="button"
										class="btn btn-sm btn-outline-primary text-nowrap text-truncate btn-block"
										data-toggle="modal" data-target="#and_selected_genres_modal">
									{{ __('genre.select_from_table') }}
								</button>

								@push('body_append')
									<div class="modal" id="and_selected_genres_modal" tabindex="-1" role="dialog"
										 aria-labelledby="exampleModalLabel"
										 aria-hidden="true">
										<div class="modal-dialog modal-lg" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title" id="exampleModalLabel">{{ __('common.select_genres') }}</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<div class="modal-body">
													<div class="container-fluid">

													</div>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-secondary"
															data-dismiss="modal">{{ __('common.close') }}</button>
												</div>
											</div>
										</div>
									</div>
								@endpush

							@endif
						</div>

						<div class="form-group">

							<select name="exclude_genres[]" data-placeholder="{{ __('book.not_search_genres') }}"
									class="exclude-genres form-control select2-multiple select2-hidden-accessible" multiple
									style="width: 100%">
								@if (isset($exclude_genres))
									@foreach ($exclude_genres as $genre)
										<option value="{{ $genre->id }}" selected="selected">{{ $genre->name }}</option>
									@endforeach
								@endif
							</select>
						</div>

						<div class="form-group">

							<button id="excluded_genres_button" type="button"
									class="btn btn-sm btn-outline-primary text-nowrap text-truncate btn-block"
									data-toggle="modal" data-target="#excluded_genres_modal">
								{{ __('genre.excluded_select_from_table') }}
							</button>

							@push('body_append')
								<div class="modal" id="excluded_genres_modal" tabindex="-1" role="dialog"
									 aria-labelledby="excludedGenresModalLabel"
									 aria-hidden="true">
									<div class="modal-dialog modal-lg" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title"
													id="excludedGenresModalLabel">{{ __('genre.excluded_select_from_table') }}</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">
												<div class="container-fluid">

												</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary"
														data-dismiss="modal">{{ __('common.close') }}</button>
											</div>
										</div>
									</div>
								</div>
							@endpush

						</div>

						<div class="form-group">

							@if (auth()->check())
								@if (auth()->user()->genres_blacklist->count())
									<label>
										<a target="_blank" href="{{ route('genre_blacklist', auth()->user()) }}">
											{{ __('') }}Жанров в вашем черном
											списке {{ auth()->user()->genres_blacklist->count() }}
										</a>
									</label>
								@else
									<label>
										<a target="_blank" href="{{ route('genre_blacklist', auth()->user()) }}">
											{{ __('') }}Настроить глобальный черный список жанров
										</a>
									</label>
								@endif
							@endif
						</div>

						<div class="form-group">
							<label for="language">{{ __('book.ti_lb') }}:</label>
							<select id="language" class="language form-control" name="language">
								<option value="">{{ __('common.any') }}</option>

								@foreach (App\Language::all() as $language)

									<option value="{{ $language->code }}"
											@if ($language->code == $input['language']) selected="selected" @endif > {{ $language->name }}</option>

								@endforeach
							</select>
						</div>

						<div class="form-group">
							<label for="originalLang">{{ __('book.ti_olb') }}:</label>
							<select id="originalLang" class="original_lang form-control" name="originalLang">
								<option value="">{{ __('common.any') }}</option>

								@foreach (App\Language::all() as $language)

									<option value="{{ $language->code }}"
											@if ($language->code == $input['originalLang']) selected="selected" @endif > {{ $language->name }}</option>

								@endforeach

							</select>
						</div>

						<div class="form-group d-flex flex-row">
							<div class="w-100 overflow-hidden">
								<select name="kw[]" class="keywords form-control select2-multiple" multiple
										data-placeholder="{{ __('book.keywords') }}"
										style="width:100%">
									@if (isset($keywords))
										@foreach ($keywords as $keyword)
											<option value="{{ $keyword->text }}" selected="selected">{{ $keyword->text }}</option>
										@endforeach
									@endif
								</select>
							</div>

							<div class="flex-shrink-1">
								<a href="javascript:void(0);" class="btn btn-light ml-1" data-container="body"
								   data-toggle="popover" data-placement="top" data-html="true"
								   data-content="{{ __('book.keywords_search_helper') }}">
									<i class="fas fa-question"></i>
								</a>
							</div>
						</div>

						<div class="form-group">
							<label>{{ trans_choice('book.extensions', 2) }}: </label>
							<select class="formats form-control select2-multiple" name="Formats[]" size="5" multiple>
								@foreach (config("litlife.book_allowed_file_extensions") as $format)
									<option value="{{ $format }}"
											@if (in_array($format, (array)$input['Formats'] ?? [])) selected="selected" @endif > {{ $format }}</option>
								@endforeach
							</select>
						</div>

						@if (auth()->check() and !in_array('read_status', $disabled_filters))

							<div class="form-group">
								<label>{{ __('book.my_read_status') }}: </label>
								<select class="form-control" name="read_status">
									@foreach (\App\Enums\ReadStatus::getValues() as $status)
										<option value="{{ $status == 'null' ? '' : $status }}"
												@if (in_array($status, (array)$input['read_status'] ?? [])) selected="selected" @endif >
											{{ trans_choice('user.my_read_status_array.'.$status, auth()->user()->gender) }}
										</option>
									@endforeach

									<option value="no_status"
											@if (in_array('no_status', (array)$input['read_status'] ?? [])) selected="selected" @endif >
										{{ trans_choice('user.my_read_status_array.no_status', auth()->user()->gender) }}
									</option>
								</select>
							</div>

						@endif

						<div class="form-group">
							<label>{{  __('book.ready_status') }}: </label>
							<select class="rs form-control" name="rs">
								<option value="">{{ __('common.any') }}</option>
								@foreach (\App\Enums\BookComplete::toArray() as $text => $code)
									<option value="{{ $text }}"
											@if ($text == $input['rs']) selected="selected" @endif > {{ __('book.text_complete.'.$text) }}</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<label>{{ __('common.order') }}: </label>


							<select class="order form-control" name="order">
								@foreach ($order_array as $code => $function)
									<option value="{{ $code }}"
											@if ($code == $input['order']) selected="selected" @endif>
										{{ __('book.sorting.'.$code.'') }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<button type="submit" class="btn btn-primary">{{ __('common.seek') }}</button>
						</div>

						<div class="form-group">
							<label>{{ __('common.view') }}: </label>
							<select class="form-control @if ($resource->isSaveSetting()) save @endif" name="view">
								@foreach (['gallery', 'table'] as $view)
									<option value="{{ $view }}"
											@if ($view == $resource->getInputValue('view')) selected="selected" @endif >
										{{ __('common.view_types.'.$view) }}</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<label>{{ __('book.search.author_gender') }}: </label>
							<select class="form-control" name="author_gender">
								<option value="">{{ __('common.any') }}</option>
								@foreach (['male', 'female'] as $code)
									<option value="{{ $code }}"
											@if ($code == $input['author_gender']) selected="selected" @endif >
										{{ __("book.search.author_gender_array.".$code) }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<label>{{ __('book.is_si') }}:</label>
							<select class="form-control" name="si">
								<option value=""> -</option>
								@foreach (['only', 'exclude'] as $code)
									<option value="{{ $code }}"
											@if ($code == $input['si']) selected="selected" @endif >
										{{ __("book.search.si_array.".$code) }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<label>{{ __('book.is_lp') }}:</label>
							<select class="form-control" name="lp">
								<option value=""> -</option>
								@foreach (['only', 'exclude'] as $code)
									<option value="{{ $code }}"
											@if ($code == $input['lp']) selected="selected" @endif >
										{{ __("book.search.lp_array.".$code) }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<label>{{ __('book.paid_access') }}:</label>
							<div class="d-flex flex-row">
								<select class="form-control @if ($resource->isSaveSetting()) save @endif" name="paid_access">
									@foreach (['any', 'paid_only', 'only_free'] as $value)
										<option value="{{ $value }}"
												@if ($value == $resource->getInputValue('paid_access')) selected="selected" @endif >
											{{ __("book.search.paid_access_array.".$value) }}
										</option>
									@endforeach
								</select>

								<a href="javascript:void(0);" class="btn btn-light ml-2" data-container="body"
								   data-toggle="popover" data-placement="top" data-html="true"
								   data-content="{{ __('book.paid_access_helper') }} {{ __('book.filter_values_are_saved') }}">
									<i class="fas fa-question"></i>
								</a>
							</div>
						</div>

						<div class="form-group">
							<label>{{ __('book.read_access') }}:</label>
							<div class="d-flex flex-row">
								<select class="form-control @if ($resource->isSaveSetting()) save @endif" name="read_access">
									@foreach (['any', 'open', 'close'] as $code)
										<option value="{{ $code }}"
												@if ($code == $resource->getInputValue('read_access')) selected="selected" @endif >
											{{ __("book.search.read_access_array.".$code) }}
										</option>
									@endforeach
								</select>

								<a href="javascript:void(0);" class="btn btn-light ml-2" data-container="body"
								   data-toggle="popover" data-placement="top" data-html="true"
								   data-content="{{ __('book.read_access_helper') }} {{ __('book.filter_values_are_saved') }}">
									<i class="fas fa-question"></i>
								</a>
							</div>
						</div>

						<div class="form-group">
							<label>{{ __('book.download_access') }}:</label>
							<div class="d-flex flex-row">
								<select class="form-control @if ($resource->isSaveSetting()) save @endif" name="download_access">
									@foreach (['any', 'open', 'close'] as $code)
										<option value="{{ $code }}"
												@if ($code == $resource->getInputValue('download_access')) selected="selected" @endif >
											{{ __("book.search.download_access_array.".$code) }}
										</option>
									@endforeach
								</select>

								<a href="javascript:void(0);" class="btn btn-light ml-2" data-container="body"
								   data-toggle="popover" data-placement="top" data-html="true"
								   data-content="{{ __('book.download_access_helper') }} {{ __('book.filter_values_are_saved') }}">
									<i class="fas fa-question"></i>
								</a>
							</div>
						</div>

						<div class="form-group">
							<label for="CoverExists">{{ __('book.cover') }}:</label>
							<select id="CoverExists" class="form-control" name="CoverExists">
								<option value=""> -</option>
								@foreach (['yes', 'no'] as $code)
									<option value="{{ $code }}"
											@if ($code == $input['CoverExists']) selected="selected" @endif >
										{{ __("book.search.cover_exists_array.".$code) }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<label for="AnnotationExists">{{ __('book.annotation') }}:</label>
							<select class="form-control" name="AnnotationExists">
								<option value=""> -</option>
								@foreach (['yes', 'no'] as $code)
									<option value="{{ $code }}"
											@if ($code == $input['AnnotationExists']) selected="selected" @endif >
										{{ __("book.search.annotation_exists_array.".$code) }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<label>{{ __('book.year_writing') }}</label>
							<div class="row">
								<div class="col-sm-6">
									<input name="write_year_after" class="form-control" type="text"
										   placeholder="{{ __('common.from') }}"
										   value="{{ $input['write_year_after'] ?? ''  }}">
								</div>
								<div class="col-sm-6">
									<input name="write_year_before" class="form-control" type="text"
										   placeholder="{{ __('common.to') }}"
										   value="{{ $input['write_year_before'] ?? ''  }}">
								</div>
							</div>
						</div>

						<div class="form-group">
							<label>{{ __('book.pi_year') }}</label>
							<div class="row">
								<div class="col-sm-6">
									<input name="publish_year_after" class="form-control" type="text"
										   placeholder="{{ __('common.from') }}"
										   value="{{ $input['publish_year_after'] ?? ''  }}">
								</div>
								<div class="col-sm-6">
									<input name="publish_year_before" class="form-control" type="text"
										   placeholder="{{ __('common.to') }}"
										   value="{{ $input['publish_year_before'] ?? ''  }}">
								</div>
							</div>
						</div>

						<div class="form-group">
							<label>{{ __('book.page_count') }}</label>
							<div class="row">
								<div class="col-sm-6">
									<input name="pages_count_min" class="form-control" type="text"
										   placeholder="{{ __('common.from') }}"
										   value="{{ $input['pages_count_min'] ?? ''  }}">
								</div>
								<div class="col-sm-6">
									<input name="pages_count_max" class="form-control" type="text"
										   placeholder="{{ __('common.to') }}"
										   value="{{ $input['pages_count_max'] ?? ''  }}">
								</div>
							</div>
						</div>

						<div class="form-group">
							<input name="publish_city" class="form-control" type="text"
								   placeholder="{{ __('book.pi_city') }}"
								   value="{{ $input['publish_city'] ?? ''  }}">
						</div>

						<div class="form-group">
							<label for="comments_exists">{{ __('model.comments') }}</label>
							<select id="comments_exists" class="form-control" name="comments_exists">
								<option value=""> -</option>
								@foreach (['yes', 'no'] as $code)
									<option value="{{ $code }}"
											@if ($code == $input['comments_exists']) selected="selected" @endif >
										{{ __("book.search.comments_exists_array.".$code) }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="form-group">
							<div class="form-check">
								<input name="hide_grouped" type="hidden" value="0"/>
								<input id="hide_grouped" name="hide_grouped" type="checkbox"
									   class="form-check-input"
									   @if ($resource->getInputValue('hide_grouped')) checked="checked" @endif value="1"/>
								<label class="form-check form-check-inline break-word" for="hide_grouped">
									{{ __('book.search.hide_grouped') }}
								</label>
							</div>
						</div>

						<div class="form-group">
							<select name="award" class="award form-control select2-hidden-accessible"
									data-placeholder="{{ __('book.search.award') }}"
									style="width: 100%">
								@if (isset($award))
									<option value="{{ $award->title }}" selected="selected">{{ $award->title }}</option>
								@endif
							</select>
						</div>

						@auth
							<div class="form-group">
								<label for="status_of_publication">{{ __('book.search.status_of_publication') }}</label>
								<select id="status_of_publication" class="form-control" name="status_of_publication">
									<option
											value="all_books">{{ __("book.search.status_of_publication_options.all_books") }}</option>
									@foreach (['published_books_only', 'private_books_only'] as $code)
										<option value="{{ $code }}"
												@if ($code == $input['status_of_publication']) selected="selected" @endif >
											{{ __("book.search.status_of_publication_options.".$code) }}
										</option>
									@endforeach
								</select>
							</div>
						@endauth
					</div>

				</form>
			</div>
		</div>
	</div>

	<div class="col-lg-8 col-md-7 list order-md-1 order-sm-2" role="main">
		@include($view_name, ['no_book_link' => true])
	</div>
</div>