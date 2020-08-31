@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

	@include ('author.edit_tab')

	@if (isset($managers) and $managers->count())

		@foreach ($managers as $manager)
			<div class="card mb-3 item" data-id="{{ $manager->id }}">
				<div class="card-body d-flex">
					<div class="w-100">
						<x-user-name :user="$manager->user"/>

						{{ __('author.manager_characters.'.$manager->character) }}

						@if ($manager->isSentForReview())
							{{ trans_choice('manager.on_check', 1) }}
						@endif

					</div>

					<div class="flex-shrink-1">

						<div class="btn-group" data-toggle="tooltip" data-placement="top"
							 title="{{ __('common.open_actions') }}">
							<button class="btn btn-light dropdown-toggle" type="button"
									id="managerDropdownMenuButton_{{ $manager->id }}"
									data-toggle="dropdown"
									aria-haspopup="true"
									aria-expanded="false">
								<i class="fas fa-ellipsis-v"></i>
							</button>
							<div class="dropdown-menu dropdown-menu-right"
								 aria-labelledby="managerDropdownMenuButton_{{ $manager->id }}">

								@can('delete', $manager)
									<a class="dropdown-item text-lowercase delete"
									   href="{{ route('managers.destroy', $manager) }}">
										{{ __('common.delete') }}
									</a>
								@endcan

								@can('salesDisable', $manager->manageable)
									<span class="dropdown-item text-lowercase pointer"
										  data-toggle="modal" data-target="#salesDisableModal_{{ $manager->id }}">
                                        {{ __('manager.disable_sales_for_the_author') }}
                                    </span>

									@push('body_append')
										<div class="modal" id="salesDisableModal_{{ $manager->id }}" tabindex="-1"
											 role="dialog"
											 aria-labelledby="salesDisableModalLabel" aria-hidden="true">
											<div class="modal-dialog" role="document">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title" id="salesDisableModalLabel"></h5>
														<button type="button" class="close" data-dismiss="modal"
																aria-label="Close">
															<span aria-hidden="true">&times;</span>
														</button>
													</div>
													<div class="modal-body">
														{{ __('manager.warning_before_disabling_sales_for_the_author') }}
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-secondary"
																data-dismiss="modal">{{ __('common.close') }}
														</button>
														<a class="btn btn-primary"
														   href="{{ route('authors.sales.disable', $manager->manageable) }}">
															{{ __('manager.disable_sales_for_the_author') }}
														</a>
													</div>
												</div>
											</div>
										</div>
									@endpush
								@endcan

							</div>
						</div>

					</div>

				</div>
			</div>
		@endforeach

	@else
		<div class="alert alert-info" role="alert">
			{{ __('manager.nothing_found') }}
		</div>
	@endif

	@can ('create', App\Manager::class)

		@if ($errors->any())
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<div class="card mb-3">
			<div class="card-body">

				<form action="{{ route('authors.managers.store', $author) }}" method="post" novalidate>

					@csrf

					<div class="form-group">
						<div class="form-row">
							<label for="user_id" class="col-md-3 col-lg-2 col-form-label">{{ __('user.id') }}</label>
							<div class="col-md-9 col-lg-10">
								<input id="user_id" name="user_id" type="text" value="{{ old('user_id') }}"
									   class="form-control {{ $errors->has('user_id') ? 'is-invalid' : '' }}" required/>
							</div>
						</div>
					</div>

					<fieldset class="form-group">
						<div class="row">
							<legend class="col-form-label col-sm-2">
								{{ __('manager.character') }}
							</legend>
							<div class="col-md-9 col-lg-10">
								@foreach (config('litlife.manager_characters') as $character)
									<div class="form-check">
										<input class="form-check-input {{ $errors->has('character') ? 'is-invalid' : '' }}"
											   type="radio"
											   name="character" id="gridRadios{{ $character }}" value="{{ $character }}"
											   @if ((!empty($manager->character)) and ($character == old('character'))) checked="checked" @endif>
										<label class="form-check-label" for="gridRadios{{ $character }}">
											{{ __('author.manager_characters.'.$character) }}
										</label>
									</div>
								@endforeach
							</div>
						</div>
					</fieldset>

					<button type="submit" class="btn btn-primary">{{ __('common.add') }}</button>

				</form>
			</div>
		</div>

	@endcan

@endsection
