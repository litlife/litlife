<?php

namespace App\Http\Controllers;

use App\Forum;
use App\Genre;
use App\Mail\TestMail;
use App\User;
use App\Variable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SettingController extends Controller
{
	/**
	 * Отображение настроек сайта
	 *
	 * @return View
	 * @throws
	 */
	public function index()
	{
		$this->authorize('admin_panel_access', User::class);

		$settings = Variable::where('name', 'settings')->first();

		$forums = Forum::find($settings->value['hide_from_main_page_forums'] ?? []);

		$genres = Genre::find($settings->value['genres_books_comments_hide_from_home_page'] ?? []);

		return view('setting.index', compact('settings', 'forums', 'genres'));
	}

	/**
	 * Сохранение настроек сайта
	 *
	 * @param Request $request
	 * @return Response
	 * @throws
	 */
	public function save(Request $request)
	{
		$this->authorize('admin_panel_access', User::class);

		$this->validate($request,
			[
				'hide_from_main_page_forums' => 'array|exists:forums,id',
				'genres_books_comments_hide_from_home_page' => 'array|exists:genres,id',
				'forbidden_words' => 'string|nullable',
				'check_words_in_comments' => 'string|nullable'
			]
		);

		$inputs['genres_books_comments_hide_from_home_page'] = $request->genres_books_comments_hide_from_home_page;

		if (!empty($request->forbidden_words)) {
			$words = preg_split("/\r\n|\n|\r/", $request->forbidden_words);

			$words2 = [];

			foreach ($words as $c => $word) {
				$word = trim($word);
				if (!empty($word))
					$words2[] = $word;
			}

			$inputs['forbidden_words'] = $words2;
		}

		if (!empty($request->check_words_in_comments)) {
			$words = preg_split("/\r\n|\n|\r/", $request->check_words_in_comments);

			$words2 = [];

			foreach ($words as $c => $word) {
				$word = trim($word);
				if (!empty($word))
					$words2[] = $word;
			}

			$inputs['check_words_in_comments'] = $words2;
		}

		$forums = Forum::find($request->input(['hide_from_main_page_forums']));

		if (!empty($forums) and count($forums) > 0)
			$inputs['hide_from_main_page_forums'] = $forums->pluck('id')->toArray();

		$settings = Variable::where('name', 'settings')->first();

		if (empty($settings)) {
			$settings = new Variable;
			$settings->name = 'settings';
		}

		$settings->value = $inputs;
		$settings->save();

		return back();
	}

	/**
	 * Обновляет все счетчики книг, авторов и т. д.
	 *
	 * @return Response
	 * @throws
	 */

	public function refresh_counters()
	{
		$this->authorize('admin_panel_access', User::class);

		Artisan::call('refresh:counters');

		return back();
	}

	public function test_mail(Request $request)
	{
		$this->authorize('admin_panel_access', User::class);

		$this->validate($request,
			[
				'email' => 'required|email',
				'test_mail_text' => 'required'
			]
		);

		config(['mail.from.address' => 'mail@litlife.club']);
		config(['mail.from.name' => 'Litlife']);
		config(['mail.dkim_selector' => 'mail']);
		config(['mail.dkim_domain' => 'litlife.club']);
		config(['mail.dkim_private_key' => '/home/litlife/dkim/litlife.club.private.pem']);
		config(['mail.dkim_algo' => 'rsa-sha256']);

		Mail::to($request->email)
			->send(new TestMail($request));

		return back()->with('test_mail_sended', true);
	}

	public function frequentlyUsedStyles()
	{
		$fonts = DB::table('user_read_styles')
			->select(DB::raw('count("font") as count, font'))
			->groupBy('font')
			->orderByDesc('count')
			->limit(5)
			->get();

		$sizes = DB::table('user_read_styles')
			->select(DB::raw('count("size") as count, size'))
			->groupBy('size')
			->orderByDesc('count')
			->limit(5)
			->get();

		$fontColors = DB::table('user_read_styles')
			->select(DB::raw('count("font_color") as count, font_color'))
			->groupBy('font_color')
			->orderByDesc('count')
			->limit(5)
			->get();

		$cardColors = DB::table('user_read_styles')
			->select(DB::raw('count("card_color") as count, card_color'))
			->groupBy('card_color')
			->orderByDesc('count')
			->limit(5)
			->get();

		$backgroundColor = DB::table('user_read_styles')
			->select(DB::raw('count("background_color") as count, background_color'))
			->groupBy('background_color')
			->orderByDesc('count')
			->limit(5)
			->get();

		$aligns = DB::table('user_read_styles')
			->select(DB::raw('count("align") as count, align'))
			->groupBy('align')
			->orderByDesc('count')
			->limit(5)
			->get();

		return view('setting.frequently_used_styles',
			compact('fonts', 'sizes', 'fontColors', 'cardColors', 'backgroundColor', 'aligns'));
	}
}
