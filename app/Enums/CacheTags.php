<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CacheTags extends Enum
{
	const FriendsAndSubscriptionsNotViewedNewsCount = 1; // The number of not viewed news of friends and subscribers
	const NewPrivateMessagesCount = 2;
	const CommentsOnModerationCount = 3; // comments_on_moderation_count
	const SmilesJsonUrl = 4;
	const ManagersOnModerationCount = 5;
	const UsersOnModerationCount = 6;
	const BooksOnModerationCount = 7;
	const BookFilesOnModerationCount = 8;
	const ComplainsOnModerationCount = 9;
	const BookKeywordsOnModerationCount = 10;
	const PostsOnModerationCount = 11;
	const NewFavoriteAuthorsBooksCount = 12;
	const UnreadNotifications = 13;
	const AuthorSaleRequestCount = 14;
	const FavoriteBooksWithUpdatesCount = 15;
	const FrozenBalance = 16;
	const LatestTopicsQuery = 17;
	const BlogPostsOnModerationCount = 18;
	const UserPrivateChaptersCount = 19;

	/*
	const GenresCount = 4; // genres_count
	const AuthorsCount = 5; // authors_count
	const BooksCount = 6; // books_count
	const SequencesCount = 7; // sequences_count
	const UsersCount = 8; // users_count
	const PostsCount = 9; // posts_count
	const CommentsCount = 10; // comments_count
 */
}
