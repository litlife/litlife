<?php

namespace App\Providers;

use App\Achievement;
use App\AdBlock;
use App\AdminNote;
use App\Attachment;
use App\Authentication\UserProvider;
use App\Author;
use App\AuthorPhoto;
use App\AuthorRepeat;
use App\Award;
use App\Blog;
use App\Book;
use App\BookFile;
use App\BookKeyword;
use App\Bookmark;
use App\BookmarkFolder;
use App\Comment;
use App\Complain;
use App\Forum;
use App\ForumGroup;
use App\Genre;
use App\Image;
use App\Keyword;
use App\Like;
use App\Manager;
use App\Message;
use App\Page;
use App\Policies\AchievementPolicy;
use App\Policies\AdBlockPolicy;
use App\Policies\AdminNotePolicy;
use App\Policies\AttachmentPolicy;
use App\Policies\AuthorPhotoPolicy;
use App\Policies\AuthorPolicy;
use App\Policies\AuthorRepeatPolicy;
use App\Policies\AwardPolicy;
use App\Policies\BlogPolicy;
use App\Policies\BookFilePolicy;
use App\Policies\BookKeywordPolicy;
use App\Policies\BookmarkFolderPolicy;
use App\Policies\BookmarkPolicy;
use App\Policies\BookPolicy;
use App\Policies\CommentPolicy;
use App\Policies\ComplainPolicy;
use App\Policies\ForumGroupPolicy;
use App\Policies\ForumPolicy;
use App\Policies\GenrePolicy;
use App\Policies\ImagePolicy;
use App\Policies\KeywordPolicy;
use App\Policies\LikePolicy;
use App\Policies\ManagerPolicy;
use App\Policies\MessagePolicy;
use App\Policies\PagePolicy;
use App\Policies\PostPolicy;
use App\Policies\SectionPolicy;
use App\Policies\SequencePolicy;
use App\Policies\SupportRequestPolicy;
use App\Policies\TextBlockPolicy;
use App\Policies\TopicPolicy;
use App\Policies\UserEmailPolicy;
use App\Policies\UserGroupPolicy;
use App\Policies\UserNotePolicy;
use App\Policies\UserPaymentTransactionPolicy;
use App\Policies\UserPolicy;
use App\Policies\UserSocialAccountPolicy;
use App\Post;
use App\Section;
use App\Sequence;
use App\SupportRequest;
use App\TextBlock;
use App\Topic;
use App\User;
use App\UserEmail;
use App\UserGroup;
use App\UserNote;
use App\UserPaymentTransaction;
use App\UserSocialAccount;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * The policy mappings for the application.
	 *
	 * @var array
	 */
	protected $policies = [
		Book::class => BookPolicy::class,
		Blog::class => BlogPolicy::class,
		Message::class => MessagePolicy::class,
		Comment::class => CommentPolicy::class,
		Post::class => PostPolicy::class,
		Topic::class => TopicPolicy::class,
		Forum::class => ForumPolicy::class,
		ForumGroup::class => ForumGroupPolicy::class,
		Manager::class => ManagerPolicy::class,
		User::class => UserPolicy::class,
		UserGroup::class => UserGroupPolicy::class,
		TextBlock::class => TextBlockPolicy::class,
		AdminNote::class => AdminNotePolicy::class,
		Attachment::class => AttachmentPolicy::class,
		Author::class => AuthorPolicy::class,
		AuthorRepeat::class => AuthorRepeatPolicy::class,
		BookFile::class => BookFilePolicy::class,
		BookKeyword::class => BookKeywordPolicy::class,
		BookmarkFolder::class => BookmarkFolderPolicy::class,
		Bookmark::class => BookmarkPolicy::class,
		Complain::class => ComplainPolicy::class,
		Genre::class => GenrePolicy::class,
		Image::class => ImagePolicy::class,
		Like::class => LikePolicy::class,
		Section::class => SectionPolicy::class,
		Sequence::class => SequencePolicy::class,
		Achievement::class => AchievementPolicy::class,
		UserSocialAccount::class => UserSocialAccountPolicy::class,
		UserEmail::class => UserEmailPolicy::class,
		Keyword::class => KeywordPolicy::class,
		UserNote::class => UserNotePolicy::class,
		Award::class => AwardPolicy::class,
		AuthorPhoto::class => AuthorPhotoPolicy::class,
		UserPaymentTransaction::class => UserPaymentTransactionPolicy::class,
		Page::class => PagePolicy::class,
		AdBlock::class => AdBlockPolicy::class,
		SupportRequest::class => SupportRequestPolicy::class
	];

	/**
	 * Register any authentication / authorization services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->registerPolicies();

		Auth::provider('old_auth', function ($app, array $config) {
			return new UserProvider();
		});
	}
}
