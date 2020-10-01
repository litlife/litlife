<?php

namespace App\Console;

use App\Author;
use App\Book;
use App\Comment;
use App\Genre;
use App\Post;
use App\Sequence;
use App\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		Commands\Old\OldAll::class,
		Commands\Old\OldAvatarsToNew::class,
		Commands\Old\OldTimeToNew::class,
		Commands\Old\OldVoteInfoToNew::class,
		Commands\Old\OldSimilarVoteToNew::class,
		Commands\Old\OldAuthorPhotosToNew::class,
		Commands\Old\OldEmailToNew::class,
		Commands\Old\OldBookToNew::class,
		Commands\Old\OldFillUserSettings::class,
		Commands\Old\OldUserSettingsToNew::class,
		Commands\Old\OldBooksConnetToNew::class,
		Commands\Old\OldUserReadStyles::class,
		Commands\Old\OldBookAccessToNew::class,
		Commands\Old\OldBookFilesToNew::class,
		Commands\Old\OldFindAndFillSourceFiles::class,
		Commands\Old\OldAllBookFillDBFromSource::class,
		Commands\Old\OldGenresAuthorSequencesToNew::class,
		Commands\Old\Bookmarks::class,
		Commands\Old\OldKeywordsToAwards::class,
		Commands\Old\OldPageCountRestore::class,
		Commands\Old\OldAligmentCharactersCountWithRemeberedPage::class,
		Commands\Old\OldFillOriginBookId::class,
		Commands\Old\OldBookGroupToNewAndUpdateCounters::class,
		Commands\Old\OldCreateAllRatingAverageForPeriod::class,
		Commands\Old\OldAuthorCreateAllRatingAverageForPeriod::class,

		Commands\Refresh\RefreshAllFavoriteCounters::class,
		Commands\Refresh\RefreshAllCommentsVotes::class,
		Commands\Refresh\RefreshBookVote::class,
		Commands\Refresh\RefreshBookFilesCount::class,
		Commands\Refresh\RefreshLevel::class,
		Commands\Refresh\RefreshBookGenresHelper::class,
		Commands\Refresh\RefreshLikesCount::class,
		Commands\Refresh\RefreshWallImagesDimesions::class,
		Commands\Refresh\RefreshCommentImagesDimesions::class,
		Commands\Refresh\RefreshMessageImagesDimesions::class,
		Commands\Refresh\RefreshPostImagesDimesions::class,
		Commands\Refresh\RefreshHtmlFromBBCode::class,
		Commands\Refresh\RefreshImagesHash::class,
		Commands\Refresh\RefreshUserCounters::class,
		Commands\Refresh\RefreshBookCounters::class,
		Commands\Refresh\RefreshAuthorCounters::class,
		Commands\Refresh\RefreshSmilesWidthHeight::class,

		Commands\Refresh\RefreshTopicCounters::class,
		Commands\Refresh\RefreshForumCounters::class,
		Commands\Refresh\RefreshDownloadExternalImages::class,
		Commands\Refresh\RefreshCounters::class,
		Commands\Refresh\RefreshBooksChangedRating::class,
		Commands\Refresh\RefreshAllBooksPageNumbers::class,
		Commands\Refresh\RefreshAuthorsChangedRating::class,
		Commands\Refresh\RefreshChildrenCount::class,
		Commands\Refresh\RefreshAllAuthorsCounters::class,
		Commands\Refresh\RefreshAllBooksCounters::class,
		Commands\Refresh\RefreshAllAuthorsRating::class,
		Commands\Refresh\RefreshAllBooksCommentsCount::class,
		Commands\Refresh\RefreshAllUsersCounters::class,
		Commands\Refresh\RefreshAllKeywordsCount::class,
		Commands\Refresh\RefreshBookAuthorsHelper::class,
		Commands\Refresh\RefreshAllUserRelations::class,
		Commands\Refresh\RefreshAllParticipationsCounters::class,
		Commands\Refresh\RefreshClearRatingForPeriods::class,
		Commands\Refresh\RefreshAllCommentsCharactersCount::class,
		Commands\Refresh\RefreshAllPostsCharactersCount::class,
		Commands\Refresh\RefreshAllAuthorsBiography::class,
		Commands\Refresh\RefreshLatestTopicsAtBottom::class,
		Commands\Refresh\RefreshAllBooksRating::class,
		Commands\Refresh\RefreshAllBooksReadStatus::class,
		Commands\Refresh\RefreshAllWaitedCounters::class,
		Commands\Refresh\RefreshGenresSlugs::class,
		Commands\Refresh\RefreshAuthorsDailyRating::class,

		Commands\Refresh\RefreshSequenceCounters::class,
		Commands\Refresh\RefreshAllSequencesCounters::class,
		Commands\Refresh\RefreshBookTitleSearchHelper::class,
		Commands\Refresh\RefreshAllUserEmailsIsValidColumn::class,

		Commands\BookAppendFromStorage::class,
		Commands\BookFillDBFromSource::class,
		Commands\BookFindWaitedAndFillDB::class,
		Commands\Test::class,
		Commands\AuthorDetectLang::class,
		Commands\BookMakeTypeAheadFiles::class,
		Commands\UserBooksProcessing::class,
		Commands\BookParseAllWaited::class,
		Commands\BookGroupAllDuplicates::class,
		Commands\UsersRemoveAllPosts::class,

		Commands\BookFile\CreateNewBookFiles::class,
		Commands\BookFile\FindAndMakeBookFiles::class,
		Commands\BookFile\DeletingOldBookFiles::class,
		Commands\BookFile\CreateNewFilesForAllBooks::class,

		Commands\ClearBookViewCountsPeriod::class,
		Commands\ClearBookViewIp::class,

		Commands\UsersMerge::class,
		Commands\FillAuthorSite::class,

		Commands\SmilesCreateJsonFile::class,
		Commands\TrashClear::class,
		Commands\DownloadExternalImages::class,
		Commands\SitemapCreate::class,

		Commands\Payment\HandleOutgoingProcessingPayments::class,
		Commands\Payment\HandleOutgoingWaitedPayments::class,
		Commands\Payment\HandleOutgoingErrorPayments::class,
		Commands\Payment\IncomingPaymentUpdate::class,
		Commands\Payment\AutoUpdateIncomingWaitedPayments::class,

		Commands\Refresh\RefreshUnitPayComissions::class,
		Commands\DeleteOutdatedNotifications::class,
		Commands\DeleteExpiredPasswordResets::class,
		Commands\AttachGroupToUserIfNotExists::class,
		Commands\RemoveAutoCreatedBookFilesIfNoChaptersExists::class,
		Commands\RemoveAutoCreatedBookFilesIfOldReadFormat::class,
		Commands\AttachUserGroupWithUserStatus::class,
		Commands\Group\AttachAuthorGroupToUsers::class,
		Commands\Group\AttachCommentMasterGroupToUsers::class,
		Commands\Group\AttachActiveCommentatorGroupToUsers::class,
		Commands\DeleteManagersFromDeletedAuthors::class,
		Commands\BookAttachmentRenameExtensions::class,
		Commands\BookSearchAndAttachmentRenameExtensions::class,
		Commands\MailingCommand::class,
		Commands\DetachInactiveEditors::class,
		Commands\AuthorSetLPStatusForAllBooks::class,
		Commands\Book\BookTextWaitedProcessingCommand::class,
		Commands\SendInvitationToTakeSurvey::class,
		Commands\Book\BookDeleteAllPagesWhereSectionWasNotFouncCommand::class,

		Commands\Fix\CorrectionOfBookIDForComments::class,
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param Schedule $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->call(function () {
			Author::cachedCountRefresh();
			Book::cachedCountRefresh();
			Sequence::cachedCountRefresh();
			User::cachedCountRefresh();
			Post::cachedCountRefresh();
			Comment::cachedCountRefresh();
			Genre::cachedCountRefresh();
		})->hourly()
			->name('force_update_counters_every_hour')
			->withoutOverlapping(30);

		$schedule->call(function () {
			Cache::forever('users_online_count', User::online()->count());
		})->everyMinute();

		$schedule->call(function () {

			if (Cache::has('authors_count_refresh')) {
				Cache::forever('authors_count', Author::accepted()->count());
				Cache::pull('authors_count_refresh');
			}

			if (Cache::has('books_count_refresh')) {
				Cache::forever('books_count', Book::accepted()->count());
				Cache::pull('books_count_refresh');
			}

			if (Cache::has('sequences_count_refresh')) {
				Cache::forever('sequences_count', Sequence::accepted()->count());
				Cache::pull('sequences_count_refresh');
			}

			if (Cache::has('users_count_refresh')) {
				Cache::forever('users_count', User::count());
				Cache::pull('users_count_refresh');
			}

			if (Cache::has('posts_count_refresh')) {
				Cache::forever('posts_count', Post::count());
				Cache::pull('posts_count_refresh');
			}

			if (Cache::has('comments_count_refresh')) {
				Cache::forever('comments_count', Comment::count());
				Cache::pull('comments_count_refresh');
			}

			if (Cache::has('genres_count_refresh')) {
				Cache::forever('genres_count', Genre::count());
				Cache::pull('genres_count_refresh');
			}

		})->everyFiveMinutes()
			->name('update_counters')
			->withoutOverlapping(2);

		$schedule->call(function () use ($schedule) {

			Artisan::call('refresh:books_changed_rating');
			Artisan::call('refresh:authors_changed_rating');
			Artisan::call('notifications:delete_outdated');
			Artisan::call('password_resets:delete_expired');

		})->hourly();

		$schedule->command('book:make_typeahead_files')
			->dailyAt('5:20');

		$schedule->command('refresh:keywords')
			->dailyAt('4:20');

		//$schedule->command('bookfiles:deleteting_old')->hourly();

		$schedule->command('bookfiles:findmake 5')
			->everyMinute()
			->withoutOverlapping(2);

		$schedule->command('book:find_wait_status_and_fill_db')
			->everyMinute()
			->withoutOverlapping(2);

		$schedule->command('refresh:clear_rating_for_periods')
			->hourly()
			->withoutOverlapping(5);

		$schedule->call(function () {
			Artisan::call('clear:book_view_ip');
		})->dailyAt('0:00');

		$schedule->call(function () {
			Artisan::call('clear:book_view_counts_period', ['period' => 'day']);
		})->dailyAt('0:01');

		$schedule->call(function () {
			Artisan::call('clear:book_view_counts_period', ['period' => 'week']);
		})->weeklyOn(1, '0:02');

		$schedule->call(function () {
			Artisan::call('clear:book_view_counts_period', ['period' => 'month']);
		})->monthlyOn(1, '0:03');

		$schedule->call(function () {
			Artisan::call('clear:book_view_counts_period', ['period' => 'year']);
		})->yearly();

		$schedule->command('refresh:authors_daily_rating')
			->dailyAt('2:01');

		$schedule->call(function () {
			Storage::put('schedule_last_run', now());
		})->everyMinute();

		$schedule->call(function () {
			Artisan::call('smile:create_json_file');
		})->dailyAt('0:04');

		$schedule->command('refresh:latest_topics_at_bottom')
			->everyTenMinutes();

		$schedule->command('user:attach_active_commentator_group_to_users')
			->dailyAt('9:05');

		$schedule->command('user:attach_comment_master_group_to_users')
			->dailyAt('9:15');

		$schedule->command('mailing:invitation_to_sell_books')
			->everyTenMinutes();

		$schedule->command('refresh:all_waited_counters')
			->hourly();

		$schedule->command('managers:delete_inactive_editors')
			->monthly();

		$schedule->command('book:text_waited_processing')
			->everyMinute()
			->withoutOverlapping(5);

		if (App::isProduction()) {
			$schedule->command('sitemap:create')
				->monthly()
				->monthlyOn(1, '3:00');
		}
		/*
				$schedule->command('survey:send_invitations', [
					'onlyUsersWhoRegisteredLaterThanTheDate' => Carbon::createFromDate(2020, 4, 1),
					'count' => 1
				])->everyThirtyMinutes();
				*/
	}

	/**
	 * Register the Closure based commands for the application.
	 *
	 * @return void
	 */
	protected function commands()
	{
		require base_path('routes/console.php');
	}
}
