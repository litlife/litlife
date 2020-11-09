<?php

namespace App\Providers;

use App;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Jenssegers\Date\Date;
use Laravel\Dusk\DuskServiceProvider;
use MailChecker;
use Request;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Paginator::useBootstrap();
		/*
		if($this->app->environment('production')) {
			URL::forceScheme('https');
		}
		*/

		if (!$this->app->environment('production')) {

			// Нет поддержки laravel 6.0
			//$this->app->register(\Way\Generators\GeneratorsServiceProvider::class);
			//$this->app->register(\Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider::class);

			$this->app->register(IdeHelperServiceProvider::class);
			$this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
			$this->app->register(\Staudenmeir\DuskUpdater\DuskServiceProvider::class);
			$this->app->register(DuskServiceProvider::class);
			$this->app->register(DuskBrowserServiceProvider::class);
		}


		if (app()->runningInConsole()) {

			$argv = Request::server('argv', null);

			if ($argv[0] == 'artisan' && Str::contains($argv[1], 'migrate')) {

				$platform = DB::getDoctrineSchemaManager()
					->getDatabasePlatform();

				$platform->registerDoctrineTypeMapping('gender', 'string');
				$platform->registerDoctrineTypeMapping('storages', 'string');
				$platform->registerDoctrineTypeMapping('morph', 'string');
				$platform->registerDoctrineTypeMapping('read_statuses', 'string');
				$platform->registerDoctrineTypeMapping('json', 'string');
				$platform->registerDoctrineTypeMapping('jsonb', 'string');
				$platform->registerDoctrineTypeMapping('_int4', 'integer');
			}
		}

		Carbon::setLocale(config('app.locale'));
		Date::setLocale(config('app.locale'));

		setlocale(LC_TIME, config('app.locale') . '_' . mb_strtoupper(config('app.locale')) . '.UTF-8');


		App\UserPhoto::observe(App\Observers\UserPhotoObserver::class);
		App\BookVote::observe(App\Observers\BookVoteObserver::class);
		App\UserRelation::observe(App\Observers\UserRelationObserver::class);
		App\Keyword::observe(App\Observers\KeywordObserver::class);
		App\BookKeyword::observe(App\Observers\BookKeywordObserver::class);
		App\Post::observe(App\Observers\PostObserver::class);
		App\Topic::observe(App\Observers\TopicObserver::class);
		App\Forum::observe(App\Observers\ForumObserver::class);
		App\ForumGroup::observe(App\Observers\ForumGroupObserver::class);
		App\Like::observe(App\Observers\LikeObserver::class);
		App\User::observe(App\Observers\UserObserver::class);
		App\UserEmail::observe(App\Observers\UserEmailObserver::class);
		App\UserEmailToken::observe(App\Observers\UserEmailTokenObserver::class);
		App\AuthorRepeat::observe(App\Observers\AuthorRepeatObserver::class);
		App\Bookmark::observe(App\Observers\BookmarkObserver::class);
		App\Blog::observe(App\Observers\BlogObserver::class);
		App\AuthorBiography::observe(App\Observers\AuthorBiographyObserver::class);
		App\AuthorPhoto::observe(App\Observers\AuthorPhotoObserver::class);
		App\Comment::observe(App\Observers\CommentObserver::class);
		App\CommentVote::observe(App\Observers\CommentVoteObserver::class);
		App\BookFile::observe(App\Observers\BookFileObserver::class);
		App\UserData::observe(App\Observers\UserDataObserver::class);
		App\BookStatus::observe(App\Observers\BookStatusObserver::class);
		App\Section::observe(App\Observers\SectionObserver::class);
		App\Attachment::observe(App\Observers\AttachmentObserver::class);
		App\Book::observe(App\Observers\BookObserver::class);
		App\Image::observe(App\Observers\ImageObserver::class);
		App\Author::observe(App\Observers\AuthorObserver::class);
		App\Sequence::observe(App\Observers\SequenceObserver::class);
		App\Message::observe(App\Observers\MessageObserver::class);
		App\Genre::observe(App\Observers\GenreObserver::class);
		App\AuthorStatus::observe(App\Observers\AuthorStatusObserver::class);
		App\Page::observe(App\Observers\PageObserver::class);
		App\Achievement::observe(App\Observers\AchievementObserver::class);
		App\AchievementUser::observe(App\Observers\AchievementUserObserver::class);
		App\BookKeywordVote::observe(App\Observers\BookKeywordVoteObserver::class);
		App\UserNote::observe(App\Observers\UserNoteObserver::class);
		App\Award::observe(App\Observers\AwardObserver::class);
		App\UserReadStyle::observe(App\Observers\UserReadStyleObserver::class);
		App\UserAgent::observe(App\Observers\UserAgentObserver::class);
		App\Complain::observe(App\Observers\ComplainObserver::class);
		App\AdminNote::observe(App\Observers\AdminNoteObserver::class);
		App\BookAward::observe(App\Observers\BookAwardObserver::class);
		App\UserPaymentDetail::observe(App\Observers\UserPaymentDetailObserver::class);
		App\BookGroup::observe(App\Observers\BookGroupObserver::class);
		App\Collection::observe(App\Observers\CollectionObserver::class);
		App\CollectedBook::observe(App\Observers\CollectedBookObserver::class);
		App\UserSurvey::observe(App\Observers\UserSurveyObserver::class);
		App\SupportQuestion::observe(App\Observers\SupportQuestionObserver::class);
		App\SupportQuestionMessage::observe(App\Observers\SupportQuestionMessageObserver::class);

		Validator::extend('wikipedia', function ($attribute, $value, $parameters, $validator) {
			$host = parse_url($value, PHP_URL_HOST);

			$ar = array_reverse(explode('.', $host));

			return @($ar[1] == "wikipedia" && $ar[0] == "org");
		});

		Validator::extend('gender', function ($attribute, $value, $parameters, $validator) {

			return in_array($value, ['male', 'female', 'unknown']);
		});

		Validator::extend('born_date_show', function ($attribute, $value, $parameters, $validator) {

			return array_key_exists($value, __('user.born_date_show_choices'));
		});

		Validator::extend('color', function ($attribute, $value, $parameters, $validator) {

			return preg_match('/\#[0-9abcdef]{6}/iu', $value);
		});

		Validator::extend('book_file_extension', function ($attribute, $value, $parameters, $validator) {

			$mimes = collect(config('litlife.allowed_mime_types'))->flatten()
				->map(function ($value) {
					return mb_strtolower($value);
				})->toArray();

			if (in_array(mb_strtolower($value->getMimeType()), $mimes))
				return true;
			else
				return false;
		});

		Validator::extend('tempmail', function ($attribute, $value, $parameters, $validator) {

			if (!MailChecker::isValid($value))
				return false;

			return true;
		});

		Validator::extend('not_email', function ($attribute, $value, $parameters, $validator) {

			$validator = Validator::make(['email' => $value], ['email' => 'email']);

			if ($validator->fails())
				return true;

			return false;
		});

		Validator::extend('alpha_single_quote', function ($attribute, $value, $parameters, $validator) {

			if (preg_match('/[^\'\-\p{L}]/iu', $value))
				return false;

			return true;
		});

		Validator::extend('alnum_at_least_three_symbols', function ($attribute, $value, $parameters, $validator) {

			if (preg_match_all('/[[:alnum:]]/iu', $value) < 3)
				return false;

			return true;
		});

		Validator::extend('alpha_left_right', function ($attribute, $value, $parameters, $validator) {

			if (preg_match('/^([^[:alnum:]]{1,1})/iu', $value))
				return false;

			if (preg_match('/([^[:alnum:]]{1,1})$/iu', $value))
				return false;

			return true;
		});

		Validator::extend('does_not_contain_url', function ($attribute, $value, $parameters, $validator) {

			if (preg_match('/www\./iu', $value))
				return false;

			return true;
		});

		Relation::morphMap([
			'blog' => 'App\Blog',
			'post' => 'App\Post',
			'author' => 'App\Author',
			'book' => 'App\Book',
			'book_file' => 'App\BookFile',
			'sequence' => 'App\Sequence',
			'comment' => 'App\Comment',
			'forum' => 'App\Forum',
			'user' => 'App\User',
			'author_biography' => 'App\AuthorBiography',
			'author_photo' => 'App\AuthorPhoto',
			'user_group' => 'App\UserGroup',
			'user_note' => 'App\UserNote',
			14 => 'App\UserIncomingPayment',
			15 => 'App\UserOutgoingPayment',
			16 => 'App\UserPurchase',
			17 => 'App\UserMoneyTransfer',
			18 => 'App\Collection',
		]);

		register_shutdown_function(function () {
			//app('tempFolders')->purge();
		});

		Blade::if('env', function ($environment) {
			return app()->environment($environment);
		});
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{

	}
}


