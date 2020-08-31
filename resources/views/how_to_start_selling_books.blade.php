@extends('layouts.app')

@section('content')

	<div class="mb-3">
		<a class="btn btn-primary"
		   href="{{ route('authors.how_to_start_selling_books') }}">{{ __('common.refresh') }}</a>
	</div>

	<ul class="list-group">

		@if (empty($user))
			<li class="list-group-item ">
				<i class="fas fa-info text-info"></i> &nbsp; {{ __('author_sale_request.please_register_and_log_in') }}
			</li>
		@else
			<li class="list-group-item text-success">
				<i class="far fa-check-circle"></i> &nbsp; {{ __('author_sale_request.you_register_and_log_in_to_the_site') }}
			</li>

			@if ($user->emails()->confirmed()->count() > 0)
				<li class="list-group-item text-success">
					<i class="far fa-check-circle"></i> &nbsp; {{ __('author_sale_request.you_have_a_confirmed_mailbox') }}
				</li>
			@else
				<li class="list-group-item">
					<i class="fas fa-info text-info"></i> &nbsp; {{ __('author_sale_request.you_dont_have_any_confirmed_mailboxes') }}
				</li>
			@endif

			@if (empty($manager))
				<li class="list-group-item text-info ">
					<i class="fas fa-info"></i> &nbsp;
					{{ __('author_sale_request.to_apply_for_sales_you_must_have_a_linked_author_page') }}
				</li>

				<li class="list-group-item">
					<div class="mb-2">
						<i class="fas fa-info text-info"></i> &nbsp;
						<h6 class="inline">{{ __('author_sale_request.please_link_the_authors_page') }}.</h6>
					</div>

					<p>В первую очередь попробуйте найти страницу автора в
						<a class="text-info" target="_blank" href="{{ route('authors') }}">поиске</a>.
						Возможно, она уже существует.</p>

					<p>Если вы не смогли найти страницу, то вам необходимо выпонить следующие действия:</p>

					<ul class="mb-3">
						<li>
							В первую очередь создайте свою страницу автора нажав на кнопку <br/>
							<a class="btn btn-outline-primary btn-sm" target="_blank" href="{{ route('authors.create') }}">
								Создать страницу автора
							</a>
						</li>
						<li>
							Далее вам необходимо добавить одну вашу книгу в одном из перечисленных форматах:
							{{ implode(', ', $fileExtensionsWhichCanExtractText) }}.
							Рекомендуется загружать файлы с
							правильной разметкой в форматах fb2 или epub.<br/>
							<a class="btn btn-outline-primary btn-sm" target="_blank" href="{{ route('books.create') }}">
								Загрузить книгу</a><br/>
							После загрузки ЛитЛайф обработает вашу книгу и постарается извлечь всю нужную информацию.
						</li>
						<li>
							На этапе заполнения описания в поле "писатели" необходимо добавить вашу страницу автора.
							Начните вводить имя автора и, когда страница будет найдена, нажмите на него.<br/>
							<span class="font-weight-bold">
								В данный момент разрешено продавать только не изданные книги,
								поэтому проверьте стоит ли метка в поле "Самиздат".
							</span><br/>
							Проверьте, верно ли заполнены остальные поля книги.
							Нажмите кнопку "Сохранить".
						</li>
						<li>
							На этапе завершения добавления книги нажмите на "опубликовать" или
							перейдите на страницу книги, нажмите на кнопку <i class="fas fa-ellipsis-h"></i>, нажмите на пункт
							"опубликовать".
						</li>
						<li>
							Если все сделано верно, то ваша книга будет опубликована в течении нескольких дней.
							По завершении публикации вы получите уведомление.
							Далее можете переходить к отправке заявки на верификацию страницы автора.
						</li>
					</ul>
					<p>Если вы нашли страницу автора или уже опубликовали свою книгу, вы можете отправить заявку на
						верификацию страницы автора:</p>
					<ul class="mb-3">
						<li>
							Перейдите на вашу страницу автора и нажмите "{{ __('author.iam_the_author') }}"
						</li>
						<li>
							Заполните заявку, отправьте и ожидайте уведомления о рассмотрении
						</li>
					</ul>
					<p class="font-weight-bold">
						Внимание! После публикации, доступ к чтению и скачиванию книги будет закрыт, если заявка на верификацию страницы автора
						еще не была рассмотрена. Но не переживайте, с момента как вы станете верифицированным автором, вы сможете открыть доступ
						к чтению или скачиванию всех книг на вашей странице автора.
					</p>
				</li>
			@elseif (!empty($manager))
				@if ($manager->isSentForReview())
					<li class="list-group-item text-info">
						<i class="far fa-clock"></i> &nbsp; {{ __('author_sale_request.your_request_for_binding_is_on_review') }}
					</li>
				@elseif ($manager->isRejected())
					<li class="list-group-item text-secondary">
						<i class="far fa-times-circle"></i> &nbsp; {{ __('author_sale_request.your_request_to_link_the_author_has_been_rejected') }}
					</li>
				@endif
			@endif

			@if (!empty($manager) and $manager->isAccepted())
				<li class="list-group-item text-success">
					<i class="far fa-check-circle"></i> &nbsp; {{ __('author_sale_request.you_have_successfully_attached_the_page_of_the_author') }}
					{{ __('author_sale_request.now_you_can_open_or_close_access_to_reading_or_downloading_your_books') }}
				</li>

				@if (empty($books) or $books->count() < 1)
					<li class="list-group-item text-info">
						<i class="fas fa-info"></i> &nbsp;
						{{ __('author_sale_request.you_dont_have_any_books_to_sell') }}
					</li>
				@endif

				@if (!$manager->can_sale)
					@if (empty($salesRequest))
						<li class="list-group-item">
							<p>
								<i class="fas fa-info"></i> &nbsp; {{ __('author_sale_request.now_you_can_apply_for_book_sales') }}
							</p>
							<p>Чтобы подать заявку вам нужно перейти на
								<a class="text-info" target="_blank" href="{{ route('authors.show', $manager->manageable) }}">
									страницу автора</a>
								и в выпадающем меню выбрать пункт "{{ __('author.sell_request') }}" или можете нажать
								<a class="text-info" target="_blank" href="{{ route('authors.sales.request', $manager->manageable) }}">сюда</a>
							</p>
						</li>
					@elseif ($salesRequest->isSentForReview() or $salesRequest->isReviewStarts())
						<li class="list-group-item">
							<i class="far fa-clock"></i> &nbsp; {{ __('author_sale_request.your_request_for_book_sales_is_on_review') }}
							Пожалуйста, ожидайте. По завершении рассмотрения вам будет отправлено уведомление
						</li>
					@elseif ($salesRequest->isRejected())
						<li class="list-group-item text-secondary">
							<i class="far fa-times-circle"></i> &nbsp;
							{{ __('author_sale_request.your_request_for_book_sales_has_been_rejected') }}
							Причина: {{ $salesRequest->review_comment }}.
							Вы можете повторно отправить заявку
							через {{ config('litlife.minimum_days_to_submit_a_new_request_for_author_sale') }}
							дней
						</li>
					@elseif ($salesRequest->isAccepted())
						<li class="list-group-item text-success">
							<i class="far fa-check-circle"></i> &nbsp; {{ __('author_sale_request.request_for_permission_to_sell_books_approved') }}
						</li>
					@endif

				@else

					<li class="list-group-item text-success">
						<i class="far fa-check-circle"></i> &nbsp; {{ __('author_sale_request.now_you_can_sell_books') }}
					</li>

					@if ($booksOnSale->count() < 1)
						<li class="list-group-item">
							<i class="fas fa-info text-info"></i> &nbsp; {{ __('author_sale_request.you_havent_sold_any_books_yet') }}
							Для того чтобы книга была выставлена на продажу вам необходимо назначить цену книги:
							<ul>
								<li>Перейдите на свою
									<a target="_blank" href="{{ route('authors.show', $manager->manageable) }}">
										страницу автора</a>.
								</li>
								<li>Затем перейдите на страницу книги, которую хотите продавать.</li>
								<li>В выпадающем меню нажмите "редактировать"</li>
								<li>Перейдите в раздел "Продажи"</li>
								<li>Введите цену и нажмите "Сохранить"</li>
							</ul>
						</li>
					@else
						<li class="list-group-item text-success"> &nbsp;
							<i class="far fa-check-circle"></i> {{ __('author_sale_request.you_have_books_for_sale') }}
						</li>
					@endif

				@endif
			@endif

			@if (empty($wallets) or $wallets->count() < 1)
				<li class="list-group-item text-info">
					<i class="fas fa-info"></i> &nbsp;
					{{ __('author_sale_request.you_have_not_yet_added_a_single_wallet_to_withdraw_funds') }}

					<a class="btn btn-outline-primary btn-sm" target="_blank"
					   href="{{ route('users.wallet.payment_details', $user) }}">
						Добавить реквизиты
					</a>
				</li>
			@else
				<li class="list-group-item text-success">
					<i class="far fa-check-circle"></i> &nbsp;

					{{ __('author_sale_request.you_have_a_wallet_for_withdrawal') }}

					@if (!empty($manager) and $manager->can_sale)

						@if ($user->balance > config('litlife.min_outgoing_payment_sum'))
							{{ __('author_sale_request.to_withdraw_funds_you_need_to_order_a_payment') }}.
							<a class="btn btn-outline-primary btn-sm" href="{{ route('users.wallet.withdrawal', $user) }}">
								{{ __('author_sale_request.order_payment') }}</a>
						@endif

					@endif
				</li>
			@endif
		@endif
	</ul>


@endsection