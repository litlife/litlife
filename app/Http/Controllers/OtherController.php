<?php

namespace App\Http\Controllers;

use App\Author;
use App\Blog;
use App\Enums\VariablesEnum;
use App\Forum;
use App\Notifications\InvitationToSellBooksNotification;
use App\Notifications\InvoiceWasSuccessfullyPaidNotification;
use App\Notifications\SendingInvitationToTakeSurveyNotification;
use App\Notifications\TestNotification;
use App\Notifications\UserHasRegisteredNotification;
use App\UrlShort;
use App\UserPaymentTransaction;
use App\Variable;
use Artesaos\SEOTools\Facades\SEOMeta;
use Exception;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Litlife\Url\Exceptions\InvalidArgument;
use Litlife\Url\Url;

class OtherController extends Controller
{
	function refer()
	{
		$ref_name = config('litlife.name_user_refrence_get_param');
		$comission_from_refrence_buyer = config('litlife.comission_from_refrence_buyer');
		$comission_from_refrence_seller = config('litlife.comission_from_refrence_seller');

		return view('user.refer', [
			'user' => auth()->user(),
			'ref_name' => $ref_name,
			'comission_from_refrence_buyer' => $comission_from_refrence_buyer,
			'comission_from_refrence_seller' => $comission_from_refrence_seller,
		]);
	}

	public function spellchecker()
	{
		header('Access-Control-Allow-Origin: *');

		try {
			new \SpellChecker\Request();

		} catch (Exception $e) {
			if (function_exists('http_response_code'))
				http_response_code(500);
			else
				header('HTTP/1.0 500 Internal Server Error');

			\SpellChecker\Request::send_response(array('error' => $e->getMessage()));
		}
	}

	public function qrcode(Request $request)
	{
		$url = filter_var($request->str, FILTER_SANITIZE_URL);

		if (empty($url))
			abort(400, __('Empty URL'));

		try {
			$url = Url::fromString($url);
		} catch (Exception $exception) {
			abort(400, __('Wrong URL'));
		}

		$routes = Route::getRoutes();

		$request = Request::create($url->getPath());

		try {
			$route = $routes->match($request);

			if ($route->isFallback)
				abort(400, __('Route not found'));

			if ($route->getName() == Route::getCurrentRoute()->getName())
				abort(400, __('The URL must not match the URL to get the qr code'));

			if (!in_array('GET', $route->methods()))
				abort(400, __('The route doesn\'t have a GET method'));
		} catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
			abort(400, __('Route not found'));
		}

		$urlShortener = UrlShort::init($url);

		return view('qrcode', ['size' => 200, 'str' => $urlShortener->getShortUrl()]);
	}

	public function sidebarShow()
	{
		return response(['show_sidebar' => true])
			->cookie('show_sidebar', true, 0);
	}

	public function sidebarHide()
	{
		return response(['show_sidebar' => false])
			->cookie('show_sidebar', false, 180000);
	}

	public function userPassAgeRestriction(Request $request)
	{
		$age = intval($request->age);

		if ($age > 21)
			$age = 21;

		return response(['can_pass_age' => $age])
			->cookie('can_pass_age', $age, 180000);
	}

	public function away(Request $request)
	{
		$whiteListHostsArray = config('away.whitelist_hosts');

		array_push($whiteListHostsArray, parse_url(config('app.url'), PHP_URL_HOST));

		SEOMeta::addMeta('robots', 'noindex');


		if ($request->has('url') and !empty($request->url)) {
			$url = html_entity_decode($request->url);

			try {
				$url = Url::fromString($url);
			} catch (InvalidArgument $exception) {
				return redirect()->route('home');
			}

			if (!empty($url) and !empty($url->getHost())) {
				$host = mb_strtolower($url->getHost());
				$host = preg_replace('/^(www\.)/iu', '', $host);

				if (!in_array($host, $whiteListHostsArray))
					return view('away_warning', ['url' => $url, 'host' => $host]);
			}
		}

		if (empty($url))
			return redirect()->route('home');
		else
			return redirect()->away((string)$url);
	}

	public function mediaBreakpointShow()
	{
		return view('media_breakpoint');
	}

	public function phpinfo()
	{
		phpinfo();
	}

	public function previewNotification()
	{
		$environment = App::environment();

		if (App::environment(['local'])) {
			$message = (new TestNotification())->toMail('test@email.com');

			$markdown = new Markdown(view(), config('mail.markdown'));

			return $markdown->render('vendor.notifications.email', $message->toArray());
		} else {
			return abort(404);
		}
	}

	public function previewUserRegisteredNotification()
	{
		$environment = App::environment();

		if (App::environment(['local'])) {
			$user = auth()->user();

			$message = (new UserHasRegisteredNotification($user, Str::random(10)))->toMail('test@email.com');

			$markdown = new Markdown(view(), config('mail.markdown'));

			return $markdown->render('vendor.notifications.email', $message->toArray());
		} else {
			return abort(404);
		}
	}

	public function previewInvoiceWasSuccessfullyPaidNotification()
	{
		$environment = App::environment();

		if (App::environment(['local'])) {
			$transaction = factory(UserPaymentTransaction::class)
				->states('incoming', 'success', 'unitpay')
				->create(['sum' => 1]);

			$message = (new InvoiceWasSuccessfullyPaidNotification($transaction))->toMail('test@email.com');

			$markdown = new Markdown(view(), config('mail.markdown'));

			return $markdown->render('vendor.notifications.email', $message->toArray());
		} else {
			return abort(404);
		}
	}

	public function previewInvitationToSellBooksNotification()
	{
		$message = (new InvitationToSellBooksNotification())->toMail();

		$markdown = new Markdown(view(), config('mail.markdown'));

		return $markdown->render('vendor.notifications.invitation_to_sell_books', $message->toArray());
	}

	public function previewBookStyles()
	{
		$faker = Factory::create();

		return view('book_styles_preview', ['faker' => $faker]);
	}

	public function previewBookStylesForEpubBooks()
	{
		$faker = Factory::create();

		return view('book_styles_preview_for_ebooks', ['faker' => $faker]);
	}

	public function previewComment()
	{
		$item = Blog::findOrFail(113462);

		return view('preview.comment', ['item' => $item]);
	}

	public function exampleTable1()
	{
		$books = Author::findOrFail(108825)
			->books()
			->with(['sequences', 'genres'])
			->limit(10)
			->get();

		$books->load(['statuses' => function ($query) {
			$query->where('user_id', auth()->id());
		}]);

		return view('example.table', ['books' => $books]);
	}

	public function exampleTable2()
	{
		$books = Author::findOrFail(108825)
			->books()
			->with(['sequences', 'genres'])
			->limit(10)
			->get();

		$books->load(['statuses' => function ($query) {
			$query->where('user_id', auth()->id());
		}]);

		return view('example.table_v2', ['books' => $books]);
	}

	public function exampleTable3()
	{
		$books = Author::findOrFail(108825)
			->books()
			->with(['sequences', 'genres'])
			->get()
			->random(20);

		$books->load(['statuses' => function ($query) {
			$query->where('user_id', auth()->id());
		}]);

		return view('example.table_v3', ['books' => $books]);
	}

	public function toBottomV1()
	{
		return view('example.to_bottom');
	}

	public function toBottomV2()
	{
		return view('example.to_bottom_2');
	}

	public function toBottomV3()
	{
		return view('example.to_bottom_3');
	}

	public function toBottomV4()
	{
		return view('example.to_bottom_4');
	}

	public function sitemapRedirect()
	{
		if (Storage::disk('public')->exists('sitemap/sitemap.xml'))
			return redirect()
				->away(Storage::disk('public')->url('sitemap/sitemap.xml'));
		else
			abort(404);
	}

	public function routeFallback()
	{
		$content = view('errors.fallback')->render();

		return response($content, 404);
	}

	public function adViewTest()
	{
		return view('ad_view_test');
	}

	public function previewSceditor()
	{
		return view('preview.sceditor');
	}

	public function previewError500()
	{
		$environment = App::environment();

		if (App::environment(['local', 'testing'])) {
			return view('errors.500');
		} else {
			abort(404);
		}
	}

	public function previewInvitationToTakeSurvey()
	{
		$environment = App::environment();

		if (App::environment(['local'])) {

			$user = auth()->user();

			$message = (new SendingInvitationToTakeSurveyNotification($user))->toMail($user);

			$markdown = new Markdown(view(), config('mail.markdown'));

			return $markdown->render('vendor.notifications.email', $message->toArray());
		} else {
			return abort(404);
		}
	}

	public function faq()
	{
		$id = Variable::where('name', VariablesEnum::ForumOfQuestions)
			->firstOrFail()
			->value;

		$forum = Forum::find($id);

		return view('faq', ['forum' => $forum ?? null]);
	}

	public function welcomeNotification()
	{
		$user = auth()->user();

		$message = (new UserHasRegisteredNotification($user))->toMail($user);

		$markdown = new Markdown(view(), config('mail.markdown'));

		return $markdown->render('vendor.notifications.email', $message->toArray());
	}
}
