<?php

namespace App\Providers;

use App;
use App\Attachment;
use App\Author;
use App\AuthorBiography;
use App\AuthorPhoto;
use App\AuthorRepeat;
use App\AuthorStatus;
use App\Blog;
use App\Book;
use App\BookFile;
use App\BookKeyword;
use App\Bookmark;
use App\BookStatus;
use App\BookVote;
use App\Comment;
use App\CommentVote;
use App\Forum;
use App\ForumGroup;
use App\Image;
use App\Keyword;
use App\Like;
use App\Message;
use App\Observers\AttachmentObserver;
use App\Observers\AuthorBiographyObserver;
use App\Observers\AuthorObserver;
use App\Observers\AuthorPhotoObserver;
use App\Observers\AuthorRepeatObserver;
use App\Observers\AuthorStatusObserver;
use App\Observers\BlogObserver;
use App\Observers\BookFileObserver;
use App\Observers\BookKeywordObserver;
use App\Observers\BookmarkObserver;
use App\Observers\BookObserver;
use App\Observers\BookStatusObserver;
use App\Observers\BookVoteObserver;
use App\Observers\CommentObserver;
use App\Observers\CommentVoteObserver;
use App\Observers\ForumGroupObserver;
use App\Observers\ForumObserver;
use App\Observers\ImageObserver;
use App\Observers\KeywordObserver;
use App\Observers\LikeObserver;
use App\Observers\MessageObserver;
use App\Observers\PostObserver;
use App\Observers\SectionObserver;
use App\Observers\SequenceObserver;
use App\Observers\TopicObserver;
use App\Observers\UserDataObserver;
use App\Observers\UserEmailObserver;
use App\Observers\UserEmailTokenObserver;
use App\Observers\UserObserver;
use App\Observers\UserPhotoObserver;
use App\Observers\UserRelationObserver;
use App\Post;
use App\Section;
use App\Sequence;
use App\Topic;
use App\User;
use App\UserData;
use App\UserEmail;
use App\UserEmailToken;
use App\UserPhoto;
use App\UserRelation;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
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
		/*
		if($this->app->environment('production')) {
			URL::forceScheme('https');
		}
		*/
		if ($this->app->environment() !== 'production') {

			// Нет поддержки laravel 6.0
			//$this->app->register(\Way\Generators\GeneratorsServiceProvider::class);
			//$this->app->register(\Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider::class);

			$this->app->register(IdeHelperServiceProvider::class);
			$this->app->register(\Staudenmeir\DuskUpdater\DuskServiceProvider::class);
			$this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
		}

		if ($this->app->environment('local', 'testing')) {
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
			}
		}

		Carbon::setLocale(config('app.locale'));
		Date::setLocale(config('app.locale'));

		setlocale(LC_TIME, config('app.locale') . '_' . mb_strtoupper(config('app.locale')) . '.UTF-8');

		UserPhoto::observe(UserPhotoObserver::class);
		BookVote::observe(BookVoteObserver::class);
		UserRelation::observe(UserRelationObserver::class);

		Keyword::observe(KeywordObserver::class);
		BookKeyword::observe(BookKeywordObserver::class);

		Post::observe(PostObserver::class);
		Topic::observe(TopicObserver::class);
		Forum::observe(ForumObserver::class);
		ForumGroup::observe(ForumGroupObserver::class);
		Like::observe(LikeObserver::class);
		User::observe(UserObserver::class);
		UserEmail::observe(UserEmailObserver::class);
		UserEmailToken::observe(UserEmailTokenObserver::class);
		AuthorRepeat::observe(AuthorRepeatObserver::class);
		Bookmark::observe(BookmarkObserver::class);
		Blog::observe(BlogObserver::class);
		AuthorBiography::observe(AuthorBiographyObserver::class);
		AuthorPhoto::observe(AuthorPhotoObserver::class);
		Comment::observe(CommentObserver::class);
		CommentVote::observe(CommentVoteObserver::class);
		BookFile::observe(BookFileObserver::class);

		UserData::observe(UserDataObserver::class);
		BookStatus::observe(BookStatusObserver::class);

		Section::observe(SectionObserver::class);
		Attachment::observe(AttachmentObserver::class);
		Book::observe(BookObserver::class);
		Image::observe(ImageObserver::class);

		Author::observe(AuthorObserver::class);
		Sequence::observe(SequenceObserver::class);

		Message::observe(MessageObserver::class);
		App\Genre::observe(App\Observers\GenreObserver::class);

		AuthorStatus::observe(AuthorStatusObserver::class);

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

			$mimes = collect(config('litlife.allowed_mime_types'))->flatten()->toArray();

			if (in_array($value->getMimeType(), $mimes))
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


