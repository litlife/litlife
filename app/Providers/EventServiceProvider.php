<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class EventServiceProvider extends ServiceProvider
{
	/**
	 * The event listener mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
		'App\Events\Event' => [
			'App\Listeners\EventListener',
		],
		SocialiteWasCalled::class => [
			// add your listeners (aka providers) here
			'SocialiteProviders\Google\GoogleExtendSocialite@handle',
			'SocialiteProviders\VKontakte\VKontakteExtendSocialite@handle',
			'SocialiteProviders\Yandex\YandexExtendSocialite@handle',
			'SocialiteProviders\Twitter\TwitterExtendSocialite@handle',
			'SocialiteProviders\Instagram\InstagramExtendSocialite@handle',
			'SocialiteProviders\YouTube\YouTubeExtendSocialite@handle',
		],

		'App\Events\BookRatingChanged' => [
			'App\Listeners\UpdateBookRating',
		],
		'App\Events\AuthorRatingChanged' => [
			'App\Listeners\UpdateAuthorRating',
		],
		'App\Events\BookViewed' => [
			'App\Listeners\UpdateBookViews',
		],
		'App\Events\AuthorViewed' => [
			'App\Listeners\UpdateAuthorViews',
		],
		'App\Events\BookFileHasBeenDownloaded' => [
			'App\Listeners\BookFileDownloadLogAppend',
			'App\Listeners\UpdateBookFileDownloadCount'
		],

		'App\Events\TopicViewed' => [
			'App\Listeners\UpdateTopicViews',
		],

		'App\Events\BookCountInGroupHasChanged' => [
			'App\Listeners\UpdateBookCountInGroup',
		],

		'Illuminate\Auth\Events\Registered' => [
			'App\Listeners\User\UserCreateDefaultBookmarkFolderListener',
			'App\Listeners\User\UserAttachDefaultGroup',
		],
		/*
				'Illuminate\Auth\Events\Attempting' => [
					'App\Listeners\LogAuthenticationAttempt',
				],

				'Illuminate\Auth\Events\Authenticated' => [
					'App\Listeners\LogAuthenticated',
				],
		  */


		'Illuminate\Auth\Events\Login' => [
			'App\Listeners\LogSuccessfulLogin',
		],

		'Illuminate\Auth\Events\Failed' => [
			'App\Listeners\LogFailedLogin',
		],

		'Illuminate\Auth\Events\PasswordReset' => [
			'App\Listeners\PasswordResetListener',
		],

		'App\Events\AuthorBooksCountChanged' => [
			'App\Listeners\UpdateAuthorRating',
			'App\Listeners\UpdateAuthorCommentCountListener',
			'App\Listeners\UpdateAuthorBooksCountListener'
		],

		'App\Events\Book\BookHasChanged' => [
			'App\Listeners\NeedCreateNewBookFiles',
		],
		'App\Events\Book\SectionsCountChanged' => [
			'App\Listeners\Book\SectionsCountRefresh'
		],
		'App\Events\Book\NotesCountChanged' => [
			'App\Listeners\Book\NotesCountRefresh'
		],
		'App\Events\Book\AttachmentsCountChanged' => [
			'App\Listeners\Book\AttachmentsCountRefresh'
		],

		'App\Events\GenreBooksCountHasChanged' => [
			'App\Listeners\UpdateGenreBooksCountListener'
		],

		'App\Events\SequenceBooksCountHasChanged' => [
			'App\Listeners\UpdateSequenceBooksCountListener'
		],

		'App\Events\BookFilesCountChanged' => [
			'App\Listeners\UpdateBookFilesCount'
		],

		'App\Events\AnchorsInSectionHasChanged' => [
			'App\Listeners\UpdateSectionAnchorsListener'
		],

		'App\Events\UserCreatedAuthorsCountChanged' => [
			'App\Listeners\UpdateUserCreatedAuthorsCount'
		],

		'App\Events\UserCreatedBooksCountChanged' => [
			'App\Listeners\UpdateUserCreatedBooksCount'
		],

		'App\Events\UserCreatedSequencesCountChanged' => [
			'App\Listeners\UpdateUserCreatedSequencesCount'
		],

		'App\Events\BookKeywordVotesChanged' => [
			'App\Listeners\UpdateBookKeywordRating'
		],

		'Illuminate\Notifications\Events\NotificationSent' => [
			'App\Listeners\FlushCachedUnreadNotificationsCount',
		],
	];

	/**
	 * Register any events for your application.
	 *
	 * @return void
	 */
	public function boot()
	{
		parent::boot();

		//
	}
}
