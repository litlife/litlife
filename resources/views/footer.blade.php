<footer id="footer" class="col-12 footer @if (!request()->cookie('sidebar_hide')) pl-260px @endif">

	<div class="pb-3">

		@can ('see_ads', \App\User::class)
			@include('ads.bottom')
		@endcan

		@if (!empty(session()->get('geoip')))
			@include('forum.latest_topics', ['topics' => \App\Topic::cachedLatestTopics()])
		@endif
	<!--noindex-->

		@php($browser = session()->get('browser'))

		@if (!empty($browser))
			@if (!$browser->isChrome() and !$browser->isFirefox() and !$browser->isOpera())
				<div class="text-small p-2">
					Для правильной работы сайта используйте <span style="color:red">только последние</span> версии браузеров:
					Chrome, Opera, Firefox.
					В других браузерах работа сайта не гарантируется. Похоже вы используете: {{ $browser->browserFamily() }}
				</div>
			@endif
		@endif

		<div class="d-flex p-2 flex-column flex-md-row">
			<div class="mb-2 text-center text-md-left" style="font-size:40px">

				<a href="{{ route('away', ['url' => 'https://vk.com/litlife.club']) }}" target="_blank" rel="nofollow"
				   style="text-decoration: none;">
					<i class="fab fa-vk color-vk"></i>
				</a>

				<a href="{{ route('away', ['url' => 'https://www.facebook.com/groups/litlife.club/']) }}" target="_blank"
				   rel="nofollow"
				   style="text-decoration: none;">
					<i class="fab fa-facebook-square color-facebook"></i>
				</a>

				<a href="{{ route('away', ['url' => 'https://ok.ru/litlife.club']) }}" target="_blank" rel="nofollow"
				   style="text-decoration: none;">
					<i class="fab fa-odnoklassniki-square color-odnoklassniki"></i>
				</a>

			</div>

			<div class="mb-2 p-2 text-center btn-margin-bottom-1 ">

				<a class="btn btn-sm btn-light text-nowrap text-truncate"
				   href="{{ route('faq') }}">
					{{ __('FAQ - frequently asked questions') }}</a>

				<a class="btn btn-sm btn-light text-nowrap text-truncate"
				   href="{{ route('authors.how_to_start_selling_books') }}">
					{{ __('How to start selling books') }}</a>
				<a class="btn btn-sm btn-light text-nowrap text-truncate" href="{{ route('users.refer') }}">
					{{ __('Affiliate program') }}</a>
				<a class="btn btn-sm btn-light text-nowrap text-truncate"
				   href="{{ route('topics.show', ['topic' => '222']) }}">
					{{ __('Ask a moderator') }}</a>
				<a class="btn btn-sm btn-light text-nowrap text-truncate" href="{{ route('rules') }}">
					{{ __('The rules of the site and forum') }}</a>
				<a class="btn btn-sm btn-light text-nowrap text-truncate" href="{{ route('rules_publish_books') }}">
					{{ __('Rules for the publication of books') }}</a>
				<a class="btn btn-sm btn-light text-nowrap text-truncate" data-toggle="collapse" href="#collapseMore"
				   role="button"
				   aria-expanded="false" aria-controls="collapseMore">
					{{ __('More') }}
				</a>

				<div class="collapse" id="collapseMore">
					@if (config('app.env') != 'testing')
						<a class="btn btn-sm btn-light" href="{{ route('books', ['order' => 'rating_avg_down']) }}">
							<h3 class="h6">{{ __('book.best_books') }}</h3>
						</a>
						<a class="btn btn-sm btn-light" href="{{ route('awards.index') }}">
							<h3 class="h6">{{ trans_choice('award.awards', 2) }}</h3></a>
						<a class="btn btn-sm btn-light" href="{{ route('keywords.index') }}">
							<h3 class="h6">{{ trans_choice('keyword.keywords', 2) }}</h3></a>
						<a class="btn btn-sm btn-light" href="{{ route('books', ['si' => 'only']) }}">
							<h3 class="h6">{{ __('book.is_si') }}</h3></a>
						<a class="btn btn-sm btn-light" href="{{ route('books', ['rs' => 'complete']) }}">
							<h3 class="h6">{{ __('book.complete_books') }}</h3></a>

						@foreach (config('litlife.book_allowed_file_extensions') as $format)
							<a class="btn btn-sm btn-light"
							   href="{{ route('books', ['Formats' => $format, 'download_access' => 'open']) }}">
								<h3 class="h6">{{ trans_choice('book.books', 2) }} {{ $format }}</h3>
							</a>
						@endforeach
					@endif
				</div>
			</div>

			<div class="mb-2 p-2 text-center text-md-right">

				<a href="https://sites-reviews.com/ru/litlife.club">
					<img srcset="https://sites-reviews.com/sites_rating/2x/litlife.club.png 2x, https://sites-reviews.com/sites_rating/3x/litlife.club.png 3x"
						 data-src="https://sites-reviews.com/sites_rating/1x/litlife.club.png" width="88" height="31" border="0"
						 alt="Рейтинг и отзывы о сайте litlife.club"/>
				</a>

				@env('production')

				<!--LiveInternet counter-->
				<script type="text/javascript"><!--
					document.write("<a rel=\"nofollow\" href='//www.liveinternet.ru/click' " +
						"target=_blank><img class=\"lazyload\" src='//counter.yadro.ru/hit?t21.1;r" +
						escape(document.referrer) + ((typeof (screen) == "undefined") ? "" :
							";s" + screen.width + "*" + screen.height + "*" + (screen.colorDepth ?
							screen.colorDepth : screen.pixelDepth)) + ";u" + escape(document.URL) +
						";" + Math.random() +
						"' alt='' title='LiveInternet: показано число просмотров за 24" +
						" часа, посетителей за 24 часа и за сегодня' " +
						"border='0' width='88' height='31'><\/a>")
					//--></script><!--/LiveInternet-->

				<!-- Yandex.Metrika informer -->
				<a href="{{ route('away', ['url' => 'https://metrika.yandex.ru/stat/?id=34745015&amp;from=informer']) }}"
				   target="_blank" rel="nofollow">
					<img src="https://informer.yandex.ru/informer/34745015/3_0_FFFFFFFF_EFEFEFFF_0_pageviews"
						 style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика"
						 title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)"
						 class="lazyload ym-advanced-informer" data-cid="34745015" data-lang="ru"/>
				</a>
				<!-- /Yandex.Metrika informer -->

				<!-- Yandex.Metrika counter -->
				<script type="text/javascript">
					(function (m, e, t, r, i, k, a) {
						m[i] = m[i] || function () {
							(m[i].a = m[i].a || []).push(arguments)
						};
						m[i].l = 1 * new Date();
						k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
					})
					(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

					ym(34745015, "init", {
						id: 34745015,
						clickmap: true,
						trackLinks: true,
						accurateTrackBounce: true
					});
				</script>
				<noscript>
					<div><img class="lazyload" src="https://mc.yandex.ru/watch/34745015"
							  style="position:absolute; left:-9999px;" alt=""/></div>
				</noscript>
				<!-- /Yandex.Metrika counter -->

				<!-- не пользуюсь статистикой гугла, поэтому и убрал
					<script>
						(function (i, s, o, g, r, a, m) {
							i['GoogleAnalyticsObject'] = r;
							i[r] = i[r] || function () {
								(i[r].q = i[r].q || []).push(arguments)
							}, i[r].l = 1 * new Date();
							a = s.createElement(o),
								m = s.getElementsByTagName(o)[0];
							a.async = 1;
							a.src = g;
							m.parentNode.insertBefore(a, m)
						})(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

						ga('create', 'UA-78712453-1', 'auto');
						ga('send', 'pageview');
					</script>
					-->
				@endenv
			</div>

		</div>


		<div class="abuse_warning text-small p-2" style="font-size:0.7rem;"></div>

		@push('body_append')
			<script type="text/javascript">
				$(function () {
					var s = 'ЛитЛайф оперативно блокирует доступ к незаконным и экстремистским материалам при получении уведомления. Согласно\n' +
						'<a href="{{ route('rules') }}"><u>правилам сайта</u></a>,\n' +
						'пользователям запрещено размещать произведения, нарушающие авторские права. ЛитЛайф не инициирует размещение, не определяет\n' +
						'получателя, не\n' +
						'утверждает и не проверяет все загружаемые произведения из-за отсутствия технической возможности. Если вы обнаружили незаконные\n' +
						'материалы\n' +
						'или нарушение авторских прав, то просим вас <a href="{{ route('for_rights_owners') }}"><u>прислать жалобу</u></a>.';

					$('footer').find('.abuse_warning').html(s);
				});
			</script>
		@endpush

		<div class="text-small  p-2" style="font-size:10px;">
			{{ request()->ip() }}

			@if (!empty(session()->get('geoip')))
				{{ session()->get('geoip')->timezone }} {{ now()->timezone(session()->get('geoip')->timezone) }}
			@endif

		</div>
		<!--/noindex-->

	</div>
</footer>

