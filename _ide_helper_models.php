<?php

// @formatter:off

/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App {
	/**
	 * App\Achievement
	 *
	 * @property int $id
	 * @property string $title
	 * @property string $description
	 * @property int $image_id
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property int $create_user_id
	 * @property-read \App\User $create_user
	 * @property-read \App\Image $image
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
	 * @method static \Illuminate\Database\Eloquent\Builder|Achievement newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Achievement newQuery()
	 * @method static \Illuminate\Database\Query\Builder|Achievement onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Achievement query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Achievement similaritySearch($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|Achievement void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereDescription($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereImageId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereTitle($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Achievement whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|Achievement withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|Achievement withoutTrashed()
	 */
	class Achievement extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\AchievementUser
	 *
	 * @property int $id
	 * @property int $user_id
	 * @property int $achievement_id
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int $create_user_id
	 * @property-read \App\Achievement|null $achievement
	 * @property-read \App\User $create_user
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|AchievementUser newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|AchievementUser newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|AchievementUser query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|AchievementUser whereAchievementId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AchievementUser whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AchievementUser whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AchievementUser whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|AchievementUser whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AchievementUser whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AchievementUser whereUserId($value)
	 */
	class AchievementUser extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\ActionLog
	 *
	 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|ActionLog query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 */
	class ActionLog extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Activity
	 *
	 * @property int $id
	 * @property string $description
	 * @property int $subject_id
	 * @property string $subject_type
	 * @property int $causer_id
	 * @property int $time
	 * @property string|null $text
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property string|null $causer_type
	 * @property string|null $log_name
	 * @property \Illuminate\Support\Collection|null $properties
	 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $causer
	 * @property-read \Illuminate\Support\Collection $changes
	 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subject
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity inLog($logNames)
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereCauserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereCauserType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereDescription($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereLogName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereProperties($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereSubjectId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereSubjectType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereUpdatedAt($value)
	 */
	class Activity extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\AdminNote
	 *
	 * @property int $id
	 * @property string $admin_noteable_type
	 * @property int $admin_noteable_id
	 * @property string|null $text
	 * @property int|null $create_user_id
	 * @property int|null $time
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property \Illuminate\Support\Carbon|null $user_edited_at Время когда пользователь отредактировал
	 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $admin_noteable
	 * @property-read \App\User|null $create_user
	 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote newQuery()
	 * @method static \Illuminate\Database\Query\Builder|AdminNote onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereAdminNoteableId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereAdminNoteableType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AdminNote whereUserEditedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|AdminNote withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|AdminNote withoutTrashed()
	 */
	class AdminNote extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Anchor
	 *
	 * @property int $id
	 * @property int $book_id
	 * @property int $section_id
	 * @property string $name
	 * @property int|null $link_to_section
	 * @method static \Illuminate\Database\Eloquent\Builder|Anchor newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Anchor newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Anchor query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Anchor whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Anchor whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Anchor whereLinkToSection($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Anchor whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Anchor whereSectionId($value)
	 */
	class Anchor extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Attachment
	 *
	 * @property int $id
	 * @property int $book_id
	 * @property string $name
	 * @property string $content_type
	 * @property int $size
	 * @property string $type
	 * @property array|null $parameters
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property string $storage
	 * @property string|null $dirname
	 * @property int|null $create_user_id
	 * @property string|null $sha256_hash
	 * @property-read \App\Book $book
	 * @property-read \App\User|null $create_user
	 * @property-read mixed $full_url200x200
	 * @property-read mixed $full_url50x50
	 * @property-read mixed $full_url90x90
	 * @property-read mixed $full_url
	 * @property-read mixed $path_to_file
	 * @property-read mixed $url
	 * @property-read mixed $full_url_sized
	 * @property-write mixed $max_height
	 * @property-write mixed $max_width
	 * @property-write mixed $quality
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment inBook($bookId)
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment newQuery()
	 * @method static \Illuminate\Database\Query\Builder|Attachment onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment parametersIn($var, $array)
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereContentType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereDirname($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereParameters($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereSha256Hash($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereSize($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereStorage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|Attachment withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|Attachment withoutTrashed()
	 */
	class Attachment extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Author
	 *
	 * @property int $id
	 * @property string $last_name
	 * @property string $first_name
	 * @property string $middle_name
	 * @property int $books_count
	 * @property int $old_rating
	 * @property string|null $lang
	 * @property int $time
	 * @property string $nickname
	 * @property string|null $home_page
	 * @property string|null $email
	 * @property int $action
	 * @property string|null $description
	 * @property int $translate_books_count
	 * @property int|null $create_user_id
	 * @property int $hide
	 * @property int|null $redirect_to_author_id
	 * @property int $comments_count
	 * @property string|null $wikipedia_url
	 * @property int $old_gender
	 * @property string|null $born_date
	 * @property string|null $born_place
	 * @property string|null $dead_date
	 * @property string|null $dead_place
	 * @property string|null $years_creation
	 * @property int|null $edit_user_id
	 * @property int|null $edit_time
	 * @property int|null $hide_time
	 * @property int|null $delete_user_id
	 * @property string|null $hide_reason
	 * @property int $user_show
	 * @property string|null $orig_last_name
	 * @property string|null $orig_first_name
	 * @property string|null $orig_middle_name
	 * @property float|null $old_vote_average
	 * @property int $votes_count
	 * @property int|null $forum_id
	 * @property int $user_lib_count
	 * @property int $view_day
	 * @property int $view_week
	 * @property int $view_month
	 * @property int $view_year
	 * @property int $view_all
	 * @property float|null $vote_average
	 * @property int $like_count
	 * @property int|null $group_id
	 * @property int|null $group_add_user
	 * @property int|null $group_add_time
	 * @property int $rating
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int|null $photo_id
	 * @property \Illuminate\Support\Carbon|null $view_updated_at
	 * @property string|null $merged_at
	 * @property \Illuminate\Support\Carbon|null $user_edited_at Время когда пользователь отредактировал
	 * @property string|null $accepted_at
	 * @property string|null $sent_for_review_at
	 * @property int|null $check_user_id
	 * @property string $gender
	 * @property int|null $status
	 * @property \Illuminate\Support\Carbon|null $status_changed_at
	 * @property int|null $status_changed_user_id
	 * @property string|null $name_helper Вспомогательный столбец для быстрого trgm поиска
	 * @property int|null $biography_id
	 * @property string|null $rejected_at
	 * @property bool $rating_changed Если рейтинг у книг изменился, то значение будет true
	 * @property int $admin_notes_count
	 * @property int $added_to_favorites_count Количество пользователей добавивших в избранное
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Activity[] $activities
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $addedToFavoritesUsers
	 * @property-read \App\AdminNote|null $admin_note
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AdminNote[] $admin_notes
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $any_books
	 * @property-read \App\Like|null $authUserLike
	 * @property-read \App\AuthorPhoto|null $avatar
	 * @property-read \App\AuthorAverageRatingForPeriod $averageRatingForPeriod
	 * @property-read \App\AuthorBiography $biography
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $books
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $compiled_books
	 * @property-read \App\User|null $create_user
	 * @property-read \App\User|null $edit_user
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $edited_books
	 * @property-read \App\Forum|null $forum
	 * @property-read mixed $full_name
	 * @property-read mixed $is_accepted
	 * @property-read mixed $is_private
	 * @property-read mixed $is_rejected
	 * @property-read mixed $is_review_starts
	 * @property-read mixed $is_sent_for_review
	 * @property mixed $name
	 * @property-read mixed $original_full_name
	 * @property-read \App\AuthorGroup|null $group
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $illustrated_books
	 * @property-read \App\Language|null $language
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AdminNote[] $latest_admin_notes
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserAuthor[] $library_users
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Like[] $likes
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Manager[] $managers
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ModeratorRequest[] $moderator_requests
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AuthorModerator[] $moderators
	 * @property-read \App\AuthorPhoto|null $photo
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AuthorPhoto[] $photos
	 * @property-read Author|null $redirect_to_author
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AuthorRepeat[] $repeats
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AuthorSaleRequest[] $sales_request
	 * @property-read \App\User|null $status_changed_user
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $translated_books
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $user2
	 * @property-read \App\AuthorStatus|null $user_status
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users_added_to_favorites
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AuthorStatus[] $users_read_statuses
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $written_books
	 * @method static \Illuminate\Database\Eloquent\Builder|Author accepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author acceptedAndSentForReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author acceptedAndSentForReviewOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author acceptedAndSentForReviewOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author acceptedOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author acceptedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author any()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author checked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author checkedAndOnCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author checkedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author fulltextSearch($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author notMerged()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author onCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author onlyChecked()
	 * @method static \Illuminate\Database\Query\Builder|Author onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author orderByRating()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author orderByRatingDayDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author orderByRatingDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author orderByRatingMonthDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author orderByRatingQuarterDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author orderByRatingWeekDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author orderByRatingYearDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Author orderStatusChangedAsc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author orderStatusChangedDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author private ()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author rememberCount($minutes = 5, $refresh = false)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author searchByNameParts($last_name = null, $first_name = null, $middle_name = null, $nickname = null)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author sentOnReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author similaritySearch($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author unaccepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author unchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereAcceptedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereAction($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereAddedToFavoritesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereAdminNotesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereBiographyId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereBooksCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereBornDate($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereBornPlace($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereCheckUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereCommentsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereDeadDate($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereDeadPlace($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereDeleteUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereDescription($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereEditTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereEditUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereEmail($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereFirstName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereForumId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereGender($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereGroupAddTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereGroupAddUser($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereGroupId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereHide($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereHideReason($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereHideTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereHomePage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereLang($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereLastName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereLikeCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereMergedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereMiddleName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereNameHelper($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereNickname($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereOldGender($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereOldRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereOldVoteAverage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereOrigFirstName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereOrigLastName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereOrigMiddleName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author wherePhotoId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereRatingChanged($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereRedirectToAuthorId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereRejectedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereSentForReviewAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereStatusChangedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereStatusChangedUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereStatusIn($statuses)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereStatusNot($status)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereTranslateBooksCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereUserEditedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereUserLibCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereUserShow($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereViewAll($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereViewDay($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereViewMonth($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereViewUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereViewWeek($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereViewYear($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereVoteAverage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereVotesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereWikipediaUrl($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Author whereYearsCreation($value)
	 * @method static \Illuminate\Database\Query\Builder|Author withTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author withUnchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author withoutCheckedScope()
	 * @method static \Illuminate\Database\Query\Builder|Author withoutTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Author wordSimilaritySearch($searchText)
	 */
	class Author extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\AuthorAverageRatingForPeriod
	 *
	 * @property int $author_id
	 * @property int|null $day_rating
	 * @property int|null $week_rating
	 * @property int|null $month_rating
	 * @property int|null $quarter_rating
	 * @property int|null $year_rating
	 * @property int|null $all_rating
	 * @property-read \App\Author $author
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorAverageRatingForPeriod newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorAverageRatingForPeriod newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorAverageRatingForPeriod query()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorAverageRatingForPeriod whereAllRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorAverageRatingForPeriod whereAuthorId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorAverageRatingForPeriod whereDayRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorAverageRatingForPeriod whereMonthRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorAverageRatingForPeriod whereQuarterRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorAverageRatingForPeriod whereWeekRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorAverageRatingForPeriod whereYearRating($value)
	 */
	class AuthorAverageRatingForPeriod extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\AuthorBiography
	 *
	 * @property int|null $author_id
	 * @property string $text
	 * @property int $edit_user_id
	 * @property int $edit_time
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property \Illuminate\Support\Carbon|null $user_edited_at Время когда пользователь отредактировал
	 * @property int $id
	 * @property bool $external_images_downloaded Скачаны ли внешние изображения
	 * @property-read \App\Author|null $author
	 * @property-read \App\User|null $edit_user
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography newQuery()
	 * @method static \Illuminate\Database\Query\Builder|AuthorBiography onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereAuthorId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereEditTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereEditUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereExternalImagesDownloaded($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereUserEditedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|AuthorBiography withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|AuthorBiography withoutTrashed()
	 */
	class AuthorBiography extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\AuthorGroup
	 *
	 * @property int $id
	 * @property string|null $last_name
	 * @property string|null $first_name
	 * @property int|null $create_user_id
	 * @property int|null $time
	 * @property int $count
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[] $authors
	 * @property-read \App\User|null $create_user
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorGroup newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorGroup newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorGroup query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorGroup whereCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorGroup whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorGroup whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorGroup whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorGroup whereFirstName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorGroup whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorGroup whereLastName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorGroup whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorGroup whereUpdatedAt($value)
	 */
	class AuthorGroup extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\AuthorModerator
	 *
	 * @property-read \App\User $create_user
	 * @property-read \App\User|null $user
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorModerator newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorModerator newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorModerator query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorModerator whereCreator(\App\User $user)
	 */
	class AuthorModerator extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\AuthorParsedData
	 *
	 * @property int $id
	 * @property string $url Ссылка на страницу автора
	 * @property string $name Имя автора
	 * @property string $email Почта автора
	 * @property string $city Город автора
	 * @property string $rating Рейтинг
	 * @property string|null $created_at
	 * @property string|null $updated_at
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorParsedData newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorParsedData newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorParsedData query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorParsedData whereCity($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorParsedData whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorParsedData whereEmail($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorParsedData whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorParsedData whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorParsedData whereRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorParsedData whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorParsedData whereUrl($value)
	 */
	class AuthorParsedData extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\AuthorPhoto
	 *
	 * @property int $author_id
	 * @property int $type
	 * @property string $name
	 * @property int $size
	 * @property int $time
	 * @property int $width
	 * @property int $height
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property int $id
	 * @property string $storage
	 * @property string|null $dirname
	 * @property int|null $create_user_id
	 * @property-read \App\Author $author
	 * @property-read \App\User|null $create_user
	 * @property-read mixed $full_url200x200
	 * @property-read mixed $full_url50x50
	 * @property-read mixed $full_url90x90
	 * @property-read mixed $full_url
	 * @property-read mixed $url
	 * @property-read mixed $full_url_sized
	 * @property-write mixed $max_height
	 * @property-write mixed $max_width
	 * @property-write mixed $quality
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto newQuery()
	 * @method static \Illuminate\Database\Query\Builder|AuthorPhoto onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereAuthorId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereDirname($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereHeight($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereSize($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereStorage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereWidth($value)
	 * @method static \Illuminate\Database\Query\Builder|AuthorPhoto withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|AuthorPhoto withoutTrashed()
	 */
	class AuthorPhoto extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\AuthorRepeat
	 *
	 * @property int $id
	 * @property int $create_user_id
	 * @property int $time
	 * @property string|null $comment
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[] $authors
	 * @property-read \App\User $create_user
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat newQuery()
	 * @method static \Illuminate\Database\Query\Builder|AuthorRepeat onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat whereComment($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|AuthorRepeat withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|AuthorRepeat withoutTrashed()
	 */
	class AuthorRepeat extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\AuthorSaleRequest
	 *
	 * @property int $id
	 * @property int $create_user_id
	 * @property int $manager_id
	 * @property int $author_id
	 * @property string $text
	 * @property string|null $review_comment
	 * @property int|null $status
	 * @property \Illuminate\Support\Carbon|null $status_changed_at
	 * @property int|null $status_changed_user_id
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property-read \App\Author $author
	 * @property-read \App\User $create_user
	 * @property-read mixed $is_accepted
	 * @property-read mixed $is_private
	 * @property-read mixed $is_rejected
	 * @property-read mixed $is_review_starts
	 * @property-read mixed $is_sent_for_review
	 * @property-read \App\Manager $manager
	 * @property-read \App\User|null $status_changed_user
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest accepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest acceptedAndSentForReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest acceptedAndSentForReviewOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest acceptedAndSentForReviewOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest acceptedOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest acceptedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest checked()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest checkedAndOnCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest checkedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest onCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest onlyChecked()
	 * @method static \Illuminate\Database\Query\Builder|AuthorSaleRequest onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest orderStatusChangedAsc()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest orderStatusChangedDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest private ()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest query()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest sentOnReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest unaccepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest unchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereAuthorId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereManagerId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereReviewComment($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereStatusChangedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereStatusChangedUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereStatusIn($statuses)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereStatusNot($status)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|AuthorSaleRequest withTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest withUnchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest withoutCheckedScope()
	 * @method static \Illuminate\Database\Query\Builder|AuthorSaleRequest withoutTrashed()
	 */
	class AuthorSaleRequest extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\AuthorStatus
	 *
	 * @property int $author_id
	 * @property int $user_id
	 * @property int $code
	 * @property int $id
	 * @property string|null $user_updated_at Время последнего изменения статуса пользователем
	 * @property int $status
	 * @property-read \App\Author $author
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorStatus newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorStatus newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorStatus query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorStatus whereAuthorId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorStatus whereCode($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorStatus whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorStatus whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorStatus whereUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|AuthorStatus whereUserUpdatedAt($value)
	 */
	class AuthorStatus extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Award
	 *
	 * @property int $id
	 * @property string $title
	 * @property string|null $description
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property int $create_user_id
	 * @property-read \App\User $create_user
	 * @method static \Illuminate\Database\Eloquent\Builder|Award newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Award newQuery()
	 * @method static \Illuminate\Database\Query\Builder|Award onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Award query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Award searchPartWord($textOrArray)
	 * @method static \Illuminate\Database\Eloquent\Builder|Award similaritySearch($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|Award void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Award whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Award whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Award whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Award whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Award whereDescription($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Award whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Award whereTitle($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Award whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|Award withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|Award withoutTrashed()
	 */
	class Award extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Blog
	 *
	 * @property int $id
	 * @property int $blog_user_id
	 * @property int $create_user_id
	 * @property \Illuminate\Support\Carbon $add_time
	 * @property string|null $bb_text
	 * @property string $text
	 * @property int $edit_time
	 * @property int $hide
	 * @property int $hide_time
	 * @property int $hide_user
	 * @property string|null $tree
	 * @property int $children_count
	 * @property int $like_count
	 * @property int $action
	 * @property int $_lft
	 * @property int $_rgt
	 * @property int|null $parent_id
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property string|null $user_edited_at Время когда пользователь отредактировал
	 * @property int $level
	 * @property bool $external_images_downloaded
	 * @property bool $image_size_defined
	 * @property bool $display_on_home_page
	 * @property int|null $user_agent_id
	 * @property int|null $status Статус поста
	 * @property string|null $status_changed_at Дата изменения статуса
	 * @property int|null $status_changed_user_id Пользователь изменивший статус
	 * @property int|null $characters_count Количество символов в посте
	 * @property-read \App\Like|null $authUserLike
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Complain[] $complaints
	 * @property-read \App\User $create_user
	 * @property-read mixed $is_accepted
	 * @property-read mixed $is_private
	 * @property-read mixed $is_rejected
	 * @property-read mixed $is_review_starts
	 * @property-read mixed $is_sent_for_review
	 * @property-read mixed $level_with_limit
	 * @property mixed $parent
	 * @property-read mixed $root
	 * @property-read mixed $tree_array
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Like[] $likes
	 * @property-read \App\User $owner
	 * @property-write mixed $b_b_text
	 * @property-read \App\User|null $status_changed_user
	 * @property-read \App\UserAgent|null $user_agent
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog accepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog acceptedAndSentForReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog acceptedAndSentForReviewOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog acceptedAndSentForReviewOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog acceptedOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog acceptedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog any()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog checked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog checkedAndOnCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog checkedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog childs($ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog descendants($ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog onCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog onlyChecked()
	 * @method static \Illuminate\Database\Query\Builder|Blog onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog orDescendants($ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog orderStatusChangedAsc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog orderStatusChangedDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog owned()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog private ()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog roots()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog sentOnReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog unaccepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog unchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereAction($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereAddTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereBbText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereBlogUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereCharactersCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereChildrenCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereDisplayOnHomePage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereEditTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereExternalImagesDownloaded($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereHide($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereHideTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereHideUser($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereImageSizeDefined($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereLevel($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereLft($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereLikeCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereParentId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereRgt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereStatusChangedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereStatusChangedUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereStatusIn($statuses)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereStatusNot($status)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereTree($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereUserAgentId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog whereUserEditedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|Blog withTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog withUnchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Blog withoutCheckedScope()
	 * @method static \Illuminate\Database\Query\Builder|Blog withoutTrashed()
	 */
	class Blog extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Book
	 *
	 * @property int $id
	 * @property string|null $genre
	 * @property string|null $author
	 * @property string|null $book_name
	 * @property string|null $nis
	 * @property int $old_rating
	 * @property int $time_add
	 * @property int|null $page_count
	 * @property int $dca
	 * @property int $rca
	 * @property string|null $ti_lb
	 * @property string|null $ti_olb
	 * @property string|null $pi_bn
	 * @property string|null $pi_pub
	 * @property string|null $pi_city
	 * @property int|null $pi_year
	 * @property string|null $pi_isbn
	 * @property string|null $series
	 * @property string|null $translator
	 * @property int $section_count
	 * @property int $action
	 * @property int $sum_of_votes
	 * @property int $create_user_id
	 * @property int $time_edit
	 * @property int $version
	 * @property int $comment_count
	 * @property string|null $moderator_info
	 * @property int $hide
	 * @property int $redirect_to_book
	 * @property int $user_read_count
	 * @property int $user_vote_count
	 * @property string|null $vote_info
	 * @property int $user_read_later_count
	 * @property int $user_read_now_count
	 * @property int|null $edit_user_id
	 * @property int $edit_time
	 * @property int $hide_time
	 * @property int $hide_user
	 * @property string|null $hide_reason
	 * @property int $type
	 * @property float|null $old_vote_average
	 * @property int $user_show
	 * @property int $user_read_not_complete_count
	 * @property string|null $old_formats
	 * @property int $secret_hide
	 * @property int $last_versions_count
	 * @property int $google_ad_hide
	 * @property int $ready_status
	 * @property float|null $vote_average
	 * @property int $like_count
	 * @property int $male_vote_count
	 * @property int $female_vote_count
	 * @property int $swear
	 * @property int $secret_hide_user_id
	 * @property float|null $male_vote_percent
	 * @property bool $is_si
	 * @property int $in_rating
	 * @property int $comments_closed
	 * @property int $hide_from_top
	 * @property int $cover_exists
	 * @property int $litres_id
	 * @property int $litres_id_by_isbn
	 * @property int|null $year_writing
	 * @property string|null $rightholder
	 * @property int|null $year_public
	 * @property bool $is_public
	 * @property int|null $age
	 * @property int $coollib_id
	 * @property string|null $secret_hide_reason
	 * @property int $user_read_not_read_count
	 * @property string|null $lang
	 * @property int|null $year
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int|null $cover_id
	 * @property bool $is_lp
	 * @property int $redaction
	 * @property int $sections_count
	 * @property string|null $rate_info
	 * @property bool $refresh_rating
	 * @property array|null $formats
	 * @property string|null $accepted_at
	 * @property int|null $check_user_id
	 * @property \Illuminate\Support\Carbon|null $connected_at
	 * @property int|null $connect_user_id
	 * @property int|null $delete_user_id
	 * @property int|null $group_id
	 * @property bool $main_in_group
	 * @property string $title
	 * @property int|null $genres_helper
	 * @property string|null $sent_for_review_at
	 * @property bool $read_access
	 * @property bool $download_access
	 * @property \Illuminate\Support\Carbon|null $user_edited_at Время когда пользователь отредактировал
	 * @property bool $need_create_new_files
	 * @property int $attachments_count
	 * @property int $notes_count
	 * @property bool $online_read_new_format Книга представляет новый или старый формат хранения страниц онлайн чтения. В старом виде страницы хранятся в sqlite базе данных, а новом в базе данных
	 * @property int $files_count Количество книжных файлов у книги
	 * @property int|null $status
	 * @property \Illuminate\Support\Carbon|null $status_changed_at
	 * @property int|null $status_changed_user_id
	 * @property bool $is_collection Книга является сборником?
	 * @property bool $annotation_exists
	 * @property bool $images_exists
	 * @property int $awards_count
	 * @property string|null $rejected_at
	 * @property int $admin_notes_count
	 * @property float|null $price Цена книги, когда она продается. Если цены нет, то она бесплатна
	 * @property int|null $free_sections_count Количество бесплатных глав с начала книги. Если 0 - то все главы платные
	 * @property \Illuminate\Support\Carbon|null $price_updated_at Дата последнего изменения цены книги
	 * @property int|null $characters_count Количество символов в тексте книги
	 * @property int|null $bought_times_count Количество сколько раз была куплена книга
	 * @property bool $copy_protection Защита от копирования
	 * @property string|null $title_search_helper Специальное поле для поиска по заголовку книги
	 * @property bool|null $forbid_to_change Запретить вносить изменения
	 * @property int|null $private_chapters_count Количество не опубликованных глав
	 * @property int|null $main_book_id
	 * @property int|null $editions_count
	 * @property float|null $previous_price Предыдущая цена
	 * @property int $added_to_favorites_count Количество пользователей добавивших в избранное
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Activity[] $activities
	 * @property-read \App\User|null $add_user
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $addedToFavoritesUsers
	 * @property-read \App\AdminNote|null $admin_note
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AdminNote[] $admin_notes
	 * @property-read \App\Section|null $annotation
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Attachment[] $attachments
	 * @property-read \App\Like|null $authUserLike
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[] $authors
	 * @property-read \App\BookAverageRatingForPeriod $average_rating_for_period
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookAward[] $awards
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookKeyword[] $book_keywords
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $boughtUsers
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookCharacterChange[] $character_change_history
	 * @property-read \App\User|null $check_user
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $commentsOrigin
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[] $compilers
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Complain[] $complaints
	 * @property-read \App\User|null $connect_user
	 * @property-read \App\Attachment|null $cover
	 * @property-read \App\User $create_user
	 * @property-read \App\User|null $deletedByUser
	 * @property-read \App\User|null $edit_user
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[] $editors
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookFile[] $files
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Genre[] $genres
	 * @property mixed $comments_count
	 * @property-read mixed $is_accepted
	 * @property-read mixed $is_private
	 * @property-read mixed $is_rejected
	 * @property-read mixed $is_review_starts
	 * @property-read mixed $is_sent_for_review
	 * @property-read \App\BookGroup|null $group
	 * @property-read \Illuminate\Database\Eloquent\Collection|Book[] $groupedBooks
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[] $illustrators
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Keyword[] $keywords
	 * @property-read \App\Language|null $language
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Activity[] $latestActivitiesItemDeleted
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AdminNote[] $latest_admin_notes
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserBook[] $library_users
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Like[] $likes
	 * @property-read Book|null $mainBook
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookCover[] $old_covers
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookStatus[] $origin_statuses
	 * @property-read \App\Language|null $originalLang
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Page[] $pages
	 * @property-read \App\BookParse $parse
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookParse[] $parses
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\PriceChangeLog[] $priceChangeLogs
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserPurchase[] $purchases
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookReadRememberPage[] $remembered_pages
	 * @property-read \App\User|null $secret_hide_user
	 * @property-read \Kalnoy\Nestedset\Collection|\App\Section[] $sections
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Sequence[] $sequences
	 * @property-read \App\Section|null $short_annotation
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookSimilarVote[] $similar_vote
	 * @property-read \Illuminate\Database\Eloquent\Collection|Book[] $similars
	 * @property-read \App\BookFile|null $source
	 * @property-read \App\User|null $status_changed_user
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookStatus[] $statuses
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookTextProcessing[] $textProcessings
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[] $translators
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $userStatuses
	 * @property-read \App\BookVote|null $userVote
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookViewIp[] $user_view_ips
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $usersAddedToFavorites
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookStatus[] $users_read_statuses
	 * @property-read \App\ViewCount $view_count
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookVote[] $votes
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $votesUsers
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[] $writers
	 * @method static \Illuminate\Database\Eloquent\Builder|Book accepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book acceptedAndSentForReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book acceptedAndSentForReviewOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book acceptedAndSentForReviewOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book acceptedOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book acceptedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book andGenre($genre_ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book any()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book anyNotTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book checked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book checkedAndOnCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book checkedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book forTable()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book free()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book fulltextSearch($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book genre($genre_ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book latestUserUpdated()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book notConnected()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book oldestUserUpdated()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book onCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book onlineReadNewFormat()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book onlyChecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book onlyDownloadAccess()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book onlyReadAccess()
	 * @method static \Illuminate\Database\Query\Builder|Book onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book orderByRatingAsc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book orderByRatingDayDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book orderByRatingDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book orderByRatingMonthDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book orderByRatingQuarterDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book orderByRatingWeekDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book orderByRatingYearDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Book orderStatusChangedAsc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book orderStatusChangedDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book paid()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book private ()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book publishCityILike($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book readAndDownloadAccess()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book readOrDownloadAccess()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book rememberCount($minutes = 5, $refresh = false)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book sentOnReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book similaritySearch($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book titleAuthorsFulltextSearch($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book unaccepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book unchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book waitedNeedCreateNewBookFiles()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereAcceptedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereAction($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereAddedToFavoritesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereAdminNotesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereAge($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereAnnotationExists($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereAttachmentsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereAuthor($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereAwardsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereBookName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereBoughtTimesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereCharactersCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereCheckUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereCommentCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereCommentsClosed($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereConnectUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereConnectedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereCoollibId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereCopyProtection($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereCoverExists($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereCoverId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereDca($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereDeleteUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereDownloadAccess($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereEditTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereEditUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereEditionsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereFemaleVoteCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereFilesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereForbidToChange($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereFormats($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereFreeSectionsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereGenre($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereGenresHelper($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereGoogleAdHide($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereGroupId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereHide($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereHideFromTop($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereHideReason($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereHideTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereHideUser($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereISBN($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereImagesExists($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereInRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereIsCollection($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereIsLp($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereIsPublic($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereIsSi($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereLang($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereLastVersionsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereLikeCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereLitresId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereLitresIdByIsbn($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereMainBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereMainInGroup($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereMaleVoteCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereMaleVotePercent($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereModeratorInfo($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereNeedCreateNewBookFilesCooldownIsOver()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereNeedCreateNewFiles($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereNis($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereNotesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereOldFormats($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereOldRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereOldVoteAverage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereOnlineReadNewFormat($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book wherePageCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book wherePagesCountRange($min = null, $max = null)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book wherePiBn($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book wherePiCity($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book wherePiIsbn($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book wherePiPub($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book wherePiYear($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book wherePreviousPrice($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book wherePrice($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book wherePriceUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book wherePrivateChaptersCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book wherePublishYearRange($from = null, $till = null)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereRateInfo($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereRca($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereReadAccess($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereReadyStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereRedaction($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereRedirectToBook($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereRefreshRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereRejectedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereRightholder($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereSecretHide($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereSecretHideReason($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereSecretHideUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereSectionCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereSectionsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereSentForReviewAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereSeries($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereStatusChangedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereStatusChangedUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereStatusIn($statuses)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereStatusNot($status)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereSumOfVotes($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereSwear($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereTiLb($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereTiOlb($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereTimeAdd($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereTimeEdit($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereTitle($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereTitleSearchHelper($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereTranslator($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereUserEditedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereUserReadCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereUserReadLaterCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereUserReadNotCompleteCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereUserReadNotReadCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereUserReadNowCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereUserShow($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereUserVoteCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereVersion($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereVoteAverage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereVoteInfo($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereWriteYearRange($from = null, $till = null)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereYear($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereYearPublic($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Book whereYearWriting($value)
	 * @method static \Illuminate\Database\Query\Builder|Book withTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book withUnchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book withoutCheckedScope()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book withoutGenre($genre_ids)
	 * @method static \Illuminate\Database\Query\Builder|Book withoutTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Book wordSimilaritySearch($searchText)
	 */
	class Book extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookAuthor
	 *
	 * @property int $book_id
	 * @property int $author_id
	 * @property int $time
	 * @property int|null $order
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int $type Автор или переводчик или редактор и тп
	 * @property-read \App\Author $author
	 * @property-read \App\Book $book
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAuthor newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAuthor newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAuthor query()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAuthor whereAuthorId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAuthor whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAuthor whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAuthor whereOrder($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAuthor whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAuthor whereType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAuthor whereUpdatedAt($value)
	 */
	class BookAuthor extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookAverageRatingForPeriod
	 *
	 * @property int $book_id
	 * @property float $day_vote_average
	 * @property int $day_votes_count
	 * @property int $week_rating
	 * @property int $week_votes_count
	 * @property int $month_rating
	 * @property int $month_votes_count
	 * @property int $quarter_rating
	 * @property int $quarter_votes_count
	 * @property int $year_rating
	 * @property int $year_votes_count
	 * @property int $day_rating
	 * @property float $week_vote_average
	 * @property float $month_vote_average
	 * @property float $quarter_vote_average
	 * @property float $year_vote_average
	 * @property int $all_rating
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod whereAllRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod whereDayRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod whereDayVoteAverage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod whereDayVotesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod whereMonthRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod whereMonthVoteAverage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod whereMonthVotesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod whereQuarterRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod whereQuarterVoteAverage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod whereQuarterVotesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod whereWeekRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod whereWeekVoteAverage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod whereWeekVotesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod whereYearRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod whereYearVoteAverage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAverageRatingForPeriod whereYearVotesCount($value)
	 */
	class BookAverageRatingForPeriod extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookAward
	 *
	 * @property int $id
	 * @property int $book_id
	 * @property int $award_id
	 * @property int|null $year
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int $create_user_id
	 * @property-read \App\Award $award
	 * @property-read \App\Book $book
	 * @property-read \App\User $create_user
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAward newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAward newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAward query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAward whereAwardId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAward whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAward whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAward whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAward whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAward whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAward whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookAward whereYear($value)
	 */
	class BookAward extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookCharacterChange
	 *
	 * @property int $id
	 * @property int $sum Количество символов, которое прибавилось или убавилось. Может быть положительным или отрицательным
	 * @property int $book_id ID книги
	 * @property int $section_id ID главы
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property-read \App\Book $book
	 * @property-read \App\Section $section
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCharacterChange newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCharacterChange newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCharacterChange query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCharacterChange whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCharacterChange whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCharacterChange whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCharacterChange whereSectionId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCharacterChange whereSum($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCharacterChange whereUpdatedAt($value)
	 */
	class BookCharacterChange extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookContributor
	 *
	 * @method static \Illuminate\Database\Eloquent\Builder|BookContributor newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookContributor newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookContributor query()
	 */
	class BookContributor extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookCover
	 *
	 * @property int $book_id
	 * @property string $name
	 * @property int $size
	 * @property int $time
	 * @property int $width
	 * @property int $height
	 * @property int $type
	 * @property string $storage
	 * @property string|null $dirname
	 * @property int|null $create_user_id
	 * @property-read \App\User|null $create_user
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCover newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCover newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCover query()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCover void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCover whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCover whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCover whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCover whereDirname($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCover whereHeight($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCover whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCover whereSize($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCover whereStorage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCover whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCover whereType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookCover whereWidth($value)
	 */
	class BookCover extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookFile
	 *
	 * @property int $book_id
	 * @property string $name
	 * @property int $size
	 * @property string $format
	 * @property int $file_size
	 * @property int|null $add_time
	 * @property int|null $create_user_id
	 * @property string $md5
	 * @property bool $original
	 * @property int $id
	 * @property int $hide
	 * @property int $hide_time
	 * @property int $hide_user
	 * @property int $version
	 * @property int $download_count
	 * @property int $download_count_update_time
	 * @property string|null $comment
	 * @property int|null $number
	 * @property int|null $edit_time
	 * @property int|null $edit_user
	 * @property int|null $name_change
	 * @property int|null $action
	 * @property object|null $error
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property string|null $accepted_at
	 * @property string|null $sent_for_review_at
	 * @property string $storage
	 * @property string|null $dirname
	 * @property bool $source
	 * @property int|null $check_user_id ID пользователя который проверил
	 * @property int|null $status
	 * @property string|null $status_changed_at
	 * @property int|null $status_changed_user_id
	 * @property string|null $rejected_at
	 * @property bool|null $auto_created Создан ли файл сайтом, после редактирования онлайн текста
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Activity[] $activities
	 * @property-read \App\User|null $add_user
	 * @property-read \App\Book $book
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Complain[] $complaints
	 * @property-read \App\User|null $create_user
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookFileDownloadLog[] $download_logs
	 * @property-read mixed $encoded_name
	 * @property mixed $extension
	 * @property-read mixed $is_accepted
	 * @property-read mixed $is_private
	 * @property-read mixed $is_rejected
	 * @property-read mixed $is_review_starts
	 * @property-read mixed $is_sent_for_review
	 * @property-read mixed $path_file
	 * @property-read mixed $url
	 * @property-write mixed $show_status
	 * @property-read \App\User|null $status_changed_user
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile accepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile acceptedAndSentForReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile acceptedAndSentForReviewOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile acceptedAndSentForReviewOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile acceptedOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile acceptedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile any()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile anyNotTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile automaticCreation()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile checked()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile checkedAndOnCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile checkedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile onCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile onlyChecked()
	 * @method static \Illuminate\Database\Query\Builder|BookFile onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile orderStatusChangedAsc()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile orderStatusChangedDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile private ()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile query()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile sentOnReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile unaccepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile unchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereAcceptedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereAction($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereAddTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereAutoCreated($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereCheckUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereComment($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereDirname($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereDownloadCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereDownloadCountUpdateTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereEditTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereEditUser($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereError($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereFileSize($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereFormat($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereHide($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereHideTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereHideUser($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereMd5($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereNameChange($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereNumber($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereOriginal($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereRejectedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereSentForReviewAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereSize($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereSource($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereStatusChangedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereStatusChangedUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereStatusIn($statuses)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereStatusNot($status)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereStorage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile whereVersion($value)
	 * @method static \Illuminate\Database\Query\Builder|BookFile withTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile withUnchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFile withoutCheckedScope()
	 * @method static \Illuminate\Database\Query\Builder|BookFile withoutTrashed()
	 */
	class BookFile extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookFileDownloadLog
	 *
	 * @property int $id
	 * @property int $book_file_id
	 * @property int|null $user_id
	 * @property int|null $time
	 * @property string $ip
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property-read \App\BookFile $book_file
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFileDownloadLog newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFileDownloadLog newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFileDownloadLog query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFileDownloadLog whereBookFileId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFileDownloadLog whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFileDownloadLog whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFileDownloadLog whereIp($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFileDownloadLog whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFileDownloadLog whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookFileDownloadLog whereUserId($value)
	 */
	class BookFileDownloadLog extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookGroup
	 *
	 * @property int $id
	 * @property int|null $create_user_id
	 * @property int $books_count
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $books
	 * @property-read \App\User|null $create_user
	 * @property mixed $rate_info
	 * @property-read \App\Book|null $main_book
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $not_main_books
	 * @method static \Illuminate\Database\Eloquent\Builder|BookGroup newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookGroup newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookGroup query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookGroup whereBooksCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookGroup whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookGroup whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookGroup whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookGroup whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookGroup whereUpdatedAt($value)
	 */
	class BookGroup extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookKeyword
	 *
	 * @property int $id
	 * @property int $book_id
	 * @property int $keyword_id
	 * @property int $create_user_id
	 * @property int $time
	 * @property int $rating
	 * @property int $hide
	 * @property int|null $hide_time
	 * @property int|null $hide_user
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property string|null $accepted_at
	 * @property string|null $sent_for_review_at
	 * @property int|null $status
	 * @property string|null $status_changed_at
	 * @property int|null $status_changed_user_id
	 * @property string|null $rejected_at
	 * @property int|null $origin_book_id
	 * @property-read \App\Book $book
	 * @property-read \App\User $create_user
	 * @property-read mixed $is_accepted
	 * @property-read mixed $is_private
	 * @property-read mixed $is_rejected
	 * @property-read mixed $is_review_starts
	 * @property-read mixed $is_sent_for_review
	 * @property-read \App\Keyword|null $keyword
	 * @property-read \App\User|null $status_changed_user
	 * @property-read \App\BookKeywordVote|null $user_vote
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookKeywordVote[] $votes
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword accepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword acceptedAndSentForReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword acceptedAndSentForReviewOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword acceptedAndSentForReviewOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword acceptedOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword acceptedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword checked()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword checkedAndOnCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword checkedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword joinKeywords()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword onCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword onlyChecked()
	 * @method static \Illuminate\Database\Query\Builder|BookKeyword onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword orderStatusChangedAsc()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword orderStatusChangedDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword private ()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword query()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword search($text)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword searchFullWord($textOrArray)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword searchPartWord($textOrArray)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword sentOnReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword unaccepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword unchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereAcceptedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereHide($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereHideTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereHideUser($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereKeywordId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereOriginBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereRating($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereRejectedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereSentForReviewAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereStatusChangedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereStatusChangedUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereStatusIn($statuses)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereStatusNot($status)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|BookKeyword withTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword withUnchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword withoutCheckedScope()
	 * @method static \Illuminate\Database\Query\Builder|BookKeyword withoutTrashed()
	 */
	class BookKeyword extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookKeywordVote
	 *
	 * @property int $book_keyword_id
	 * @property int $create_user_id
	 * @property int $vote
	 * @property int $time
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int $id
	 * @property-read \App\BookKeyword $book_keyword
	 * @property-read \App\User $create_user
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeywordVote newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeywordVote newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeywordVote query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeywordVote whereBookKeywordId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeywordVote whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeywordVote whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeywordVote whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeywordVote whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeywordVote whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeywordVote whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookKeywordVote whereVote($value)
	 */
	class BookKeywordVote extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookParse
	 *
	 * @property int $id
	 * @property int $book_id ID книги над которой производилось действие
	 * @property string|null $started_at Время начала парсинга
	 * @property \Illuminate\Support\Carbon|null $succeed_at Время когда процедура успешно завершилась
	 * @property \Illuminate\Support\Carbon|null $failed_at Время когда когда произошла ошибка во время процедуры
	 * @property array|null $parse_errors Ошибки которые появились при обработке
	 * @property array|null $options Опции которые будут отправлены в обработчик
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $waited_at
	 * @property int|null $create_user_id
	 * @property-read \App\Book $book
	 * @property-read \App\User|null $create_user
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse failedParse()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse query()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse succeedParse()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse waited()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse whereFailedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse whereOptions($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse whereParseErrors($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse whereStartedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse whereSucceedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookParse whereWaitedAt($value)
	 */
	class BookParse extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookReadRememberPage
	 *
	 * @property int $book_id
	 * @property int $user_id
	 * @property int $time
	 * @property int $page
	 * @property string $updated_at
	 * @property int|null $inner_section_id
	 * @property int|null $characters_count Количество символов в тексте книги на момент последнего прочтения
	 * @property-read \App\Book $book
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|BookReadRememberPage newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookReadRememberPage newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookReadRememberPage query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookReadRememberPage whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookReadRememberPage whereCharactersCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookReadRememberPage whereInnerSectionId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookReadRememberPage wherePage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookReadRememberPage whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookReadRememberPage whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookReadRememberPage whereUserId($value)
	 */
	class BookReadRememberPage extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookSequence
	 *
	 * @property int $book_id
	 * @property int $sequence_id
	 * @property int|null $number
	 * @property int|null $order
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSequence newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSequence newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSequence query()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSequence whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSequence whereNumber($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSequence whereOrder($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSequence whereSequenceId($value)
	 */
	class BookSequence extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookSimilar
	 *
	 * @property int $id
	 * @property int $book_id
	 * @property int $book_id2
	 * @property int $rating
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookSimilarVote[] $votes
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilar newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilar newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilar query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilar whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilar whereBookId2($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilar whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilar whereRating($value)
	 */
	class BookSimilar extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookSimilarVote
	 *
	 * @property int|null $book_similar_id
	 * @property int $create_user_id
	 * @property int $vote
	 * @property int $time
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int $book_id
	 * @property int $other_book_id
	 * @property int $id
	 * @property-read \App\Book $book
	 * @property-read \App\User $create_user
	 * @property-read \App\Book $other_book
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilarVote newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilarVote newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilarVote query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilarVote whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilarVote whereBookSimilarId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilarVote whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilarVote whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilarVote whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilarVote whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilarVote whereOtherBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilarVote whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilarVote whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSimilarVote whereVote($value)
	 */
	class BookSimilarVote extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookSourceFile
	 *
	 * @property int $book_file_id
	 * @property string|null $source_file_name
	 * @property mixed|null $error
	 * @property int|null $failed_job_id
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSourceFile newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSourceFile newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSourceFile query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSourceFile whereBookFileId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSourceFile whereError($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSourceFile whereFailedJobId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookSourceFile whereSourceFileName($value)
	 */
	class BookSourceFile extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookStatus
	 *
	 * @property int $book_id
	 * @property int $user_id
	 * @property int $code
	 * @property int $time
	 * @property \Illuminate\Support\Carbon|null $user_updated_at
	 * @property int $id
	 * @property int $status
	 * @property int|null $origin_book_id
	 * @property-read \App\Book $book
	 * @property-read \App\Book|null $originBook
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|BookStatus newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookStatus newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookStatus onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookStatus query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookStatus whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookStatus whereCode($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookStatus whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookStatus whereOriginBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookStatus whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookStatus whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookStatus whereUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookStatus whereUserUpdatedAt($value)
	 */
	class BookStatus extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookTextProcessing
	 *
	 * @property int $id
	 * @property int $book_id ID обрабатываемой книги
	 * @property int $create_user_id ID пользователя создавшего обработку
	 * @property bool $remove_bold Удалить "жирное" выделение во всем тексте. Будут удалены теги b, strong
	 * @property bool $remove_extra_spaces Убрать лишние пробелы перед текстом внутри параграфов
	 * @property bool $split_into_chapters Попробовать разбить тексты на главы. Главы разбваются если в тексте будут найдены параграфы с текстом "Глава (номер главы)", "Эпилог", "Предисловие"
	 * @property bool $convert_new_lines_to_paragraphs Попробовать разбить тексты на главы. Главы разбваются если в тексте будут найдены параграфы с текстом "Глава (номер главы)", "Эпилог", "Предисловие"
	 * @property bool $add_a_space_after_the_first_hyphen_in_the_paragraph Попробовать разбить тексты на главы. Главы разбваются если в тексте будут найдены параграфы с текстом "Глава (номер главы)", "Эпилог", "Предисловие"
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $started_at Время начала обработки
	 * @property \Illuminate\Support\Carbon|null $completed_at Время окончания обработки
	 * @property bool $remove_italics Удалить "жирное" выделение во всем тексте. Будут удалены теги b, strong
	 * @property bool $remove_spaces_before_punctuations_marks Убрать лишние пробелы перед текстом внутри параграфов
	 * @property bool $add_spaces_after_punctuations_marks Убрать лишние пробелы перед текстом внутри параграфов
	 * @property bool $merge_paragraphs_if_there_is_no_dot_at_the_end Слить параграфы, если в конце текста параграфа нет точки. Например: "<p>Текст текст</p><p> текст текст.</p><p>Текст текст.</p>" станет таким: "<p>Текст текст текст текст.</p><p>Текст текст.</p>"
	 * @property bool $tidy_chapter_names Сделать аккуратными названия глав. Например: "ГЛАВА   1" будет приведено в "Глава 1"
	 * @property bool $remove_empty_paragraphs Удалить "жирное" выделение во всем тексте. Будут удалены теги b, strong
	 * @property-read \App\Book $book
	 * @property-read \App\User $create_user
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing query()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing waited()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereAddASpaceAfterTheFirstHyphenInTheParagraph($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereAddSpacesAfterPunctuationsMarks($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereCompletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereConvertNewLinesToParagraphs($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereMergeParagraphsIfThereIsNoDotAtTheEnd($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereRemoveBold($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereRemoveEmptyParagraphs($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereRemoveExtraSpaces($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereRemoveItalics($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereRemoveSpacesBeforePunctuationsMarks($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereSplitIntoChapters($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereStartedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereTidyChapterNames($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTextProcessing whereUpdatedAt($value)
	 */
	class BookTextProcessing extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookTranslator
	 *
	 * @property int $book_id
	 * @property int $translator_id
	 * @property int $time
	 * @property int|null $order
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTranslator newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTranslator newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTranslator query()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTranslator whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTranslator whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTranslator whereOrder($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTranslator whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTranslator whereTranslatorId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookTranslator whereUpdatedAt($value)
	 */
	class BookTranslator extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookView
	 *
	 * @property int $book_id
	 * @property int|null $user_id
	 * @property int $time
	 * @property string|null $ip
	 * @method static \Illuminate\Database\Eloquent\Builder|BookView newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookView newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookView query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookView whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookView whereIp($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookView whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookView whereUserId($value)
	 */
	class BookView extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookViewIp
	 *
	 * @property string $ip
	 * @property int $book_id
	 * @property int $count
	 * @property-read \App\Book $book
	 * @method static \Illuminate\Database\Eloquent\Builder|BookViewIp newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookViewIp newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookViewIp query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookViewIp whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookViewIp whereCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookViewIp whereIp($value)
	 */
	class BookViewIp extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookVote
	 *
	 * @property int $book_id
	 * @property int $create_user_id
	 * @property int $rate
	 * @property int $time
	 * @property int $hide
	 * @property int $vote
	 * @property string|null $ip
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property int $id
	 * @property \Illuminate\Support\Carbon $user_updated_at
	 * @property int|null $origin_book_id
	 * @property-read \App\Book $book
	 * @property-read \App\User $create_user
	 * @property-read \App\Book|null $originBook
	 * @property-read \App\User|null $user
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote newQuery()
	 * @method static \Illuminate\Database\Query\Builder|BookVote onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote query()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote whereHide($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote whereIp($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote whereOriginBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote whereRate($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote whereUserUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookVote whereVote($value)
	 * @method static \Illuminate\Database\Query\Builder|BookVote withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|BookVote withoutTrashed()
	 */
	class BookVote extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Bookmark
	 *
	 * @property int $id
	 * @property int $create_user_id
	 * @property string|null $url_old
	 * @property string $title
	 * @property int $time
	 * @property int|null $folder_id
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property string $url
	 * @property bool $new
	 * @property-read \App\BookmarkFolder $bookmark_folder
	 * @property-read \App\User $create_user
	 * @property-read \App\BookmarkFolder|null $folder
	 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark newQuery()
	 * @method static \Illuminate\Database\Query\Builder|Bookmark onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereFolderId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereNew($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereTitle($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereUrl($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereUrlOld($value)
	 * @method static \Illuminate\Database\Query\Builder|Bookmark withTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark withoutFolder()
	 * @method static \Illuminate\Database\Query\Builder|Bookmark withoutTrashed()
	 */
	class Bookmark extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\BookmarkFolder
	 *
	 * @property int $id
	 * @property int $create_user_id
	 * @property string $title
	 * @property int $time
	 * @property int $bookmark_count
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Bookmark[] $bookmarks
	 * @property-read \App\User $create_user
	 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder newQuery()
	 * @method static \Illuminate\Database\Query\Builder|BookmarkFolder onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder whereBookmarkCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder whereTitle($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|BookmarkFolder withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|BookmarkFolder withoutTrashed()
	 */
	class BookmarkFolder extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\CollectedBook
	 *
	 * @property int $id
	 * @property int $collection_id ID подборки
	 * @property int $book_id Книга
	 * @property int $create_user_id collected_books.create_user_id
	 * @property int|null $number Номер
	 * @property string|null $comment Комментарий
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property-read \App\Book $book
	 * @property-read \App\Collection $collection
	 * @property-read \App\User $create_user
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereCollectionId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereComment($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereNumber($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereUpdatedAt($value)
	 */
	class CollectedBook extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Collection
	 *
	 * @property int $id
	 * @property string $title Название
	 * @property string|null $description Описание
	 * @property int $who_can_add Кто может добавлять книги
	 * @property int $who_can_comment Кто может комментировать
	 * @property string|null $lang Язык
	 * @property string|null $url Ссылка на подборку на внешнем сайте
	 * @property string|null $url_title Название ссылки
	 * @property int|null $cover_id collection.cover_id
	 * @property int $create_user_id ID пользователя, который создал
	 * @property int $books_count Количество книг
	 * @property int|null $comments_count Количество комментариев
	 * @property int|null $added_to_favorites_users_count Количество раз добавлена в избранное
	 * @property int|null $views_count Количество просмотров
	 * @property int|null $like_count Количество лайков
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property string|null $latest_updates_at Дата последних обновлений в подборке
	 * @property int|null $status Кто видит подборку
	 * @property string|null $status_changed_at Дата изменения поля кто видит подборку
	 * @property int|null $status_changed_user_id Пользователь изменивший поле кто видит подборку
	 * @property int|null $users_count Количество участников
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $addedToFavoritesUsers
	 * @property-read \App\Like|null $authUserLike
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CollectionUser[] $collectionUser
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Complain[] $complaints
	 * @property-read \App\User $create_user
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserSubscriptionsEventNotification[] $eventNotificationSubscriptions
	 * @property-read mixed $is_accepted
	 * @property-read mixed $is_private
	 * @property-read mixed $is_rejected
	 * @property-read mixed $is_review_starts
	 * @property-read mixed $is_sent_for_review
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $latest_books
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Like[] $likes
	 * @property-write mixed $who_can_see
	 * @property-read \App\User|null $status_changed_user
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $usersAddedToFavorites
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserFavoriteCollection[] $usersAddedToFavoritesPivot
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection accepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection acceptedAndSentForReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection acceptedAndSentForReviewOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection acceptedAndSentForReviewOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection acceptedOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection acceptedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection any()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection checked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection checkedAndOnCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection checkedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection fulltextSearch($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection onCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection onlyChecked()
	 * @method static \Illuminate\Database\Query\Builder|Collection onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection orderByBooksCount()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection orderByLikesCount()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection orderStatusChangedAsc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection orderStatusChangedDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection private ()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection seeEveryone()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection sentOnReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection unaccepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection unchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection userSees($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereAddedToFavoritesUsersCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereBooksCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereCommentsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereCoverId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereDescription($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereLang($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereLatestUpdatesAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereLikeCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereStatusChangedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereStatusChangedUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereStatusIn($statuses)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereStatusNot($status)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereTitle($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereUrl($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereUrlTitle($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereUsersCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereViewsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereWhoCanAdd($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection whereWhoCanComment($value)
	 * @method static \Illuminate\Database\Query\Builder|Collection withTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection withUnchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Collection withoutCheckedScope()
	 * @method static \Illuminate\Database\Query\Builder|Collection withoutTrashed()
	 */
	class Collection extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\CollectionUser
	 *
	 * @property int $id
	 * @property int $collection_id ID подборки
	 * @property int $user_id ID пользователя
	 * @property int $create_user_id ID пользователя
	 * @property string|null $description Описание
	 * @property bool $can_user_manage Может добавлять, редактировать, удалять других пользователей
	 * @property bool $can_edit Может редактировать подборку
	 * @property bool $can_add_books Может добавлять книги в подборку
	 * @property bool $can_remove_books Может удалять книги из подборки
	 * @property bool $can_edit_books_description Может редактировать описания книг
	 * @property bool $can_comment Может комментировать
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property-read \App\Collection $collection
	 * @property-read \App\User $create_user
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser newQuery()
	 * @method static \Illuminate\Database\Query\Builder|CollectionUser onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCanAddBooks($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCanComment($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCanEdit($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCanEditBooksDescription($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCanRemoveBooks($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCanUserManage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCollectionId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereDescription($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CollectionUser whereUserId($value)
	 * @method static \Illuminate\Database\Query\Builder|CollectionUser withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|CollectionUser withoutTrashed()
	 */
	class CollectionUser extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Comment
	 *
	 * @property int $id
	 * @property int $commentable_id
	 * @property int $old_commentable_type
	 * @property int $create_user_id
	 * @property int $time
	 * @property string $text
	 * @property string|null $ip_old
	 * @property int $vote_up
	 * @property int $vote_down
	 * @property int $is_spam
	 * @property mixed|null $user_vote_for_spam
	 * @property string|null $bb_text
	 * @property int|null $edit_user_id
	 * @property int $edit_time
	 * @property int $reputation_count
	 * @property int $hide
	 * @property int $hide_time
	 * @property int $hide_user
	 * @property string|null $complain_user_ids
	 * @property int $checked
	 * @property int $vote
	 * @property int $action
	 * @property string|null $tree
	 * @property int $children_count
	 * @property int $hide_from_top
	 * @property int $_lft
	 * @property int $_rgt
	 * @property int|null $parent_id
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property string|null $user_edited_at Время когда пользователь отредактировал
	 * @property string|null $accepted_at
	 * @property string|null $sent_for_review_at
	 * @property string $commentable_type
	 * @property int $level
	 * @property bool $external_images_downloaded
	 * @property int|null $status
	 * @property string|null $status_changed_at
	 * @property int|null $status_changed_user_id
	 * @property bool $image_size_defined
	 * @property string $ip
	 * @property int|null $user_agent_id
	 * @property string|null $rejected_at
	 * @property int|null $characters_count
	 * @property int|null $origin_commentable_id
	 * @property-read \App\Book $book
	 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $commentable
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Complain[] $complaints
	 * @property-read \App\User $create_user
	 * @property-read mixed $is_accepted
	 * @property-read mixed $is_private
	 * @property-read mixed $is_rejected
	 * @property-read mixed $is_review_starts
	 * @property-read mixed $is_sent_for_review
	 * @property-read mixed $level_with_limit
	 * @property mixed $parent
	 * @property-read mixed $root
	 * @property-read mixed $tree_array
	 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $originCommentable
	 * @property-write mixed $b_b_text
	 * @property-read \App\User|null $status_changed_user
	 * @property-read \App\User $user
	 * @property-read \App\BookVote|null $userBookVote
	 * @property-read \App\UserAgent|null $user_agent
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\CommentVote[] $votes
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment accepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment acceptedAndSentForReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment acceptedAndSentForReviewOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment acceptedAndSentForReviewOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment acceptedOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment acceptedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment any()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment author($author)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment book()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment checked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment checkedAndOnCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment checkedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment childs($ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment descendants($ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment fulltextSearch($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment notTransferred()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment onCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment onlyChecked()
	 * @method static \Illuminate\Database\Query\Builder|Comment onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment orDescendants($ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment orderByOriginFirstAndLatest($commentable)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment orderStatusChangedAsc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment orderStatusChangedDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment private ()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment roots()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment sentOnReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment sequence($sequence)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment showOnHomePage()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment transferred()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment unaccepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment unchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereAcceptedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereAction($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereBbText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCharactersCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereChecked($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereChildrenCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereComplainUserIds($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereEditTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereEditUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereExternalImagesDownloaded($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereHide($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereHideFromTop($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereHideTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereHideUser($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereImageSizeDefined($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereIp($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereIpOld($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereIsSpam($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereLevel($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereLft($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOldCommentableType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereOriginCommentableId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereParentId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereRejectedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereReputationCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereRgt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereSentForReviewAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereStatusChangedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereStatusChangedUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereStatusIn($statuses)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereStatusNot($status)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereTree($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUserAgentId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUserEditedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUserVoteForSpam($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereVote($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereVoteDown($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereVoteUp($value)
	 * @method static \Illuminate\Database\Query\Builder|Comment withTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment withUnchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Comment withoutCheckedScope()
	 * @method static \Illuminate\Database\Query\Builder|Comment withoutTrashed()
	 */
	class Comment extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\CommentVote
	 *
	 * @property int $comment_id
	 * @property int $create_user_id
	 * @property int $vote
	 * @property int $time
	 * @property string|null $ip
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int $id
	 * @property-read \App\Comment $comment
	 * @property-read \App\User $create_user
	 * @method static \Illuminate\Database\Eloquent\Builder|CommentVote newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|CommentVote newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|CommentVote query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|CommentVote whereCommentId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CommentVote whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CommentVote whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CommentVote whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|CommentVote whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CommentVote whereIp($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CommentVote whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CommentVote whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CommentVote whereVote($value)
	 */
	class CommentVote extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Complain
	 *
	 * @property int $id
	 * @property string $complainable_type
	 * @property int $complainable_id
	 * @property int $create_user_id
	 * @property string $text
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property string|null $accepted_at
	 * @property int|null $status
	 * @property \Illuminate\Support\Carbon|null $status_changed_at
	 * @property int|null $status_changed_user_id
	 * @property string|null $sent_for_review_at
	 * @property string|null $rejected_at
	 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $complainable
	 * @property-read \App\User $create_user
	 * @property-read mixed $is_accepted
	 * @property-read mixed $is_private
	 * @property-read mixed $is_rejected
	 * @property-read mixed $is_review_starts
	 * @property-read mixed $is_sent_for_review
	 * @property-read \App\User|null $status_changed_user
	 * @property-read \App\User|null $user
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain accepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain acceptedAndSentForReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain acceptedAndSentForReviewOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain acceptedAndSentForReviewOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain acceptedOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain acceptedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain checked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain checkedAndOnCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain checkedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain onCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain onlyChecked()
	 * @method static \Illuminate\Database\Query\Builder|Complain onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain orderStatusChangedAsc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain orderStatusChangedDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain private ()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain sentOnReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain unaccepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain unchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereAcceptedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereComplainableId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereComplainableType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereRejectedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereSentForReviewAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereStatusChangedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereStatusChangedUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereStatusIn($statuses)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereStatusNot($status)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|Complain withTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain withUnchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Complain withoutCheckedScope()
	 * @method static \Illuminate\Database\Query\Builder|Complain withoutTrashed()
	 */
	class Complain extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Conversation
	 *
	 * @property int $id
	 * @property int $latest_message_id
	 * @property int $messages_count
	 * @property int $participations_count
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Message[] $messages
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Participation[] $participations
	 * @method static \Illuminate\Database\Eloquent\Builder|Conversation newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Conversation newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Conversation query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereLatestMessageId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereMessagesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereParticipationsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereUsers($user, $user2)
	 */
	class Conversation extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\CurseWord
	 *
	 * @property int $id
	 * @property string $text
	 * @method static \Illuminate\Database\Eloquent\Builder|CurseWord newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|CurseWord newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|CurseWord query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|CurseWord whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|CurseWord whereText($value)
	 */
	class CurseWord extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\DatabaseNotification
	 *
	 * @property string $id
	 * @property string $type
	 * @property string $notifiable_type
	 * @property int $notifiable_id
	 * @property array $data
	 * @property \Illuminate\Support\Carbon|null $read_at
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $notifiable
	 * @method static \Illuminate\Notifications\DatabaseNotificationCollection|static[] all($columns = ['*'])
	 * @method static \Illuminate\Notifications\DatabaseNotificationCollection|static[] get($columns = ['*'])
	 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotification newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotification newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotification query()
	 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotification whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotification whereData($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotification whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotification whereNotifiableId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotification whereNotifiableType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotification whereReadAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotification whereType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|DatabaseNotification whereUpdatedAt($value)
	 */
	class DatabaseNotification extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\FailedJobs
	 *
	 * @property int $id
	 * @property string $connection
	 * @property string $queue
	 * @property string $payload
	 * @property string $exception
	 * @property string $failed_at
	 * @property-read \App\Book|null $book
	 * @method static \Illuminate\Database\Eloquent\Builder|FailedJobs inBook($bookId)
	 * @method static \Illuminate\Database\Eloquent\Builder|FailedJobs newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|FailedJobs newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|FailedJobs query()
	 * @method static \Illuminate\Database\Eloquent\Builder|FailedJobs whereConnection($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|FailedJobs whereException($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|FailedJobs whereFailedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|FailedJobs whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|FailedJobs wherePayload($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|FailedJobs whereQueue($value)
	 */
	class FailedJobs extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Forum
	 *
	 * @property int $id
	 * @property string $name
	 * @property string|null $description
	 * @property int $create_time
	 * @property int|null $create_user_id
	 * @property int $topic_count
	 * @property int $post_count
	 * @property int|null $last_topic_id
	 * @property int|null $last_post_id
	 * @property int|null $forum_group_id
	 * @property string|null $obj_type
	 * @property int|null $obj_id
	 * @property int $hide
	 * @property int $hide_time
	 * @property int $hide_user
	 * @property int $min_message_count
	 * @property bool $private
	 * @property string|null $private_user_ids
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property string|null $user_edited_at Время когда пользователь отредактировал
	 * @property bool|null $autofix_first_post_in_created_topics
	 * @property bool|null $order_topics_based_on_fix_post_likes
	 * @property bool $is_idea_forum
	 * @property-read \App\User|null $create_user
	 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $forumable
	 * @property-read \App\ForumGroup|null $group
	 * @property-read \App\Post|null $last_post
	 * @property-read \App\Topic|null $last_topic
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Post[] $posts
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Topic[] $topics
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UsersAccessToForum[] $user_access
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users_with_access
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum any()
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum fulltextSearch($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum newQuery()
	 * @method static \Illuminate\Database\Query\Builder|Forum onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum public ()
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereAutofixFirstPostInCreatedTopics($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereCreateTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereDescription($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereForumGroupId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereHide($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereHideTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereHideUser($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereIsIdeaForum($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereLastPostId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereLastTopicId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereMinMessageCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereObjId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereObjType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereOrderTopicsBasedOnFixPostLikes($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum wherePostCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum wherePrivate($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum wherePrivateUserIds($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereTopicCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Forum whereUserEditedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|Forum withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|Forum withoutTrashed()
	 */
	class Forum extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\ForumGroup
	 *
	 * @property int $id
	 * @property string $name
	 * @property int $create_time
	 * @property int $create_user_id
	 * @property string|null $forum_sort
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property int|null $image_id forum_group.image_id
	 * @property-read \App\User $create_user
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Forum[] $forums
	 * @property-read \App\Image|null $image
	 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup any()
	 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup newQuery()
	 * @method static \Illuminate\Database\Query\Builder|ForumGroup onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup orderBySettings()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereCreateTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereForumSort($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereImageId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ForumGroup whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|ForumGroup withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|ForumGroup withoutTrashed()
	 */
	class ForumGroup extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Genre
	 *
	 * @property int $id
	 * @property int|null $old_genre_group_id Старый ID главного жанра
	 * @property string $name
	 * @property string|null $fb_code
	 * @property int $book_count
	 * @property int $age
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property string|null $slug Слаг
	 * @property int|null $genre_group_id Старый ID главного жанра
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $books
	 * @property-read \Illuminate\Database\Eloquent\Collection|Genre[] $childGenres
	 * @property-read Genre|null $group
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre main()
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre notMain()
	 * @method static \Illuminate\Database\Query\Builder|Genre onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre parseIds($ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre rememberCount($minutes = 5, $refresh = false)
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre search($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereAge($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereBookCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereFbCode($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereGenreGroupId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereIdWithSlug($id)
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereOldGenreGroupId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereSlug($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Genre whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|Genre withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|Genre withoutTrashed()
	 */
	class Genre extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\GenreGroup
	 *
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Genre[] $genres
	 * @property-write mixed $name
	 * @method static \Illuminate\Database\Eloquent\Builder|GenreGroup newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|GenreGroup newQuery()
	 * @method static \Illuminate\Database\Query\Builder|GenreGroup onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|GenreGroup query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|GenreGroup whereIdWithSlug($id)
	 * @method static \Illuminate\Database\Query\Builder|GenreGroup withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|GenreGroup withoutTrashed()
	 */
	class GenreGroup extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Image
	 *
	 * @property int $id
	 * @property string $type
	 * @property int $add_time
	 * @property int $create_user_id
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property string $name
	 * @property int $size
	 * @property string|null $md5
	 * @property string $storage
	 * @property string|null $dirname
	 * @property string|null $sha256_hash
	 * @property string|null $phash
	 * @property-read \App\User $create_user
	 * @property-read mixed $full_url200x200
	 * @property-read mixed $full_url50x50
	 * @property-read mixed $full_url90x90
	 * @property-read mixed $full_url
	 * @property-read mixed $url
	 * @property-read mixed $full_url_sized
	 * @property-write mixed $max_height
	 * @property-write mixed $max_width
	 * @property-write mixed $quality
	 * @method static \Illuminate\Database\Eloquent\Builder|Image any()
	 * @method static \Illuminate\Database\Eloquent\Builder|Image md5Hash($hash)
	 * @method static \Illuminate\Database\Eloquent\Builder|Image newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Image newQuery()
	 * @method static \Illuminate\Database\Query\Builder|Image onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Image pHash($hash)
	 * @method static \Illuminate\Database\Eloquent\Builder|Image query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Image setSize($height)
	 * @method static \Illuminate\Database\Eloquent\Builder|Image sha256Hash($hash)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Image whereAddTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Image whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Image whereDirname($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Image whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Image whereMd5($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Image whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Image wherePhash($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Image whereSha256Hash($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Image whereSize($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Image whereStorage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Image whereType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Image whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|Image withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|Image withoutTrashed()
	 */
	class Image extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Invitation
	 *
	 * @property int $id
	 * @property string $email
	 * @property string $token
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @method static \Illuminate\Database\Eloquent\Builder|Invitation newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Invitation newQuery()
	 * @method static \Illuminate\Database\Query\Builder|Invitation onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Invitation query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereEmail($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereToken($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Invitation whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|Invitation withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|Invitation withoutTrashed()
	 */
	class Invitation extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Keyword
	 *
	 * @property int $id
	 * @property string $text
	 * @property int $count
	 * @property int $action
	 * @property int $hide
	 * @property int|null $hide_time
	 * @property int|null $hide_user
	 * @property int|null $create_user_id
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property string|null $accepted_at
	 * @property string|null $sent_for_review_at
	 * @property int|null $status
	 * @property string|null $status_changed_at
	 * @property int|null $status_changed_user_id
	 * @property string|null $rejected_at
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookKeyword[] $book_keywords
	 * @property-read \App\User|null $create_user
	 * @property-read mixed $is_accepted
	 * @property-read mixed $is_private
	 * @property-read mixed $is_rejected
	 * @property-read mixed $is_review_starts
	 * @property-read mixed $is_sent_for_review
	 * @property-read \App\User|null $status_changed_user
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword accepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword acceptedAndSentForReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword acceptedAndSentForReviewOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword acceptedAndSentForReviewOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword acceptedOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword acceptedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword any()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword checked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword checkedAndOnCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword checkedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword onCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword onlyChecked()
	 * @method static \Illuminate\Database\Query\Builder|Keyword onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword orderStatusChangedAsc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword orderStatusChangedDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword private ()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword search($text)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword searchFullWord($textOrArray)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword searchPartWord($textOrArray)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword sentOnReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword unaccepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword unchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereAcceptedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereAction($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereHide($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereHideTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereHideUser($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereRejectedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereSentForReviewAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereStatusChangedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereStatusChangedUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereStatusIn($statuses)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereStatusNot($status)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|Keyword withTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword withUnchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Keyword withoutCheckedScope()
	 * @method static \Illuminate\Database\Query\Builder|Keyword withoutTrashed()
	 */
	class Keyword extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Language
	 *
	 * @property int $id
	 * @property string $name
	 * @property string $code
	 * @property int $priority
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $books
	 * @method static \Illuminate\Database\Eloquent\Builder|Language disableCache()
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Language newModelQuery()
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Language newQuery()
	 * @method static \Illuminate\Database\Query\Builder|Language onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Language query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCode($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Language whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Language whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Language whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Language wherePriority($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Language whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Language withCacheCooldownSeconds($seconds = null)
	 * @method static \Illuminate\Database\Query\Builder|Language withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|Language withoutTrashed()
	 */
	class Language extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Like
	 *
	 * @property int $id
	 * @property string $likeable_type
	 * @property int $likeable_id
	 * @property int $create_user_id
	 * @property int $time
	 * @property string $ip
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property-read \App\User $create_user
	 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $likeable
	 * @method static \Illuminate\Database\Eloquent\Builder|Like newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Like newQuery()
	 * @method static \Illuminate\Database\Query\Builder|Like onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Like query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Like whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Like whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Like whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Like whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Like whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Like whereIp($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Like whereLikeableId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Like whereLikeableType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Like whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Like whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|Like withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|Like withoutTrashed()
	 */
	class Like extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Mailing
	 *
	 * @property int $id
	 * @property string $email Почта
	 * @property int|null $priority Приоритет отправки
	 * @property string|null $name Имя пользователя
	 * @property \Illuminate\Support\Carbon|null $sent_at Время отправки сообщения
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @method static \Illuminate\Database\Eloquent\Builder|Mailing newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Mailing newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Mailing query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Mailing sent()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Mailing waited()
	 * @method static \Illuminate\Database\Eloquent\Builder|Mailing whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Mailing whereEmail($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Mailing whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Mailing whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Mailing wherePriority($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Mailing whereSentAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Mailing whereUpdatedAt($value)
	 */
	class Mailing extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Manager
	 *
	 * @property int $id
	 * @property int $create_user_id
	 * @property int $user_id
	 * @property string $character
	 * @property int $manageable_id
	 * @property int $add_time
	 * @property int $hide
	 * @property int $hide_time
	 * @property int $hide_user
	 * @property string|null $comment
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property string|null $accepted_at
	 * @property int|null $check_user_id
	 * @property string $manageable_type
	 * @property string|null $rejected_at
	 * @property int|null $status
	 * @property \Illuminate\Support\Carbon|null $status_changed_at
	 * @property int|null $status_changed_user_id
	 * @property string|null $sent_for_review_at
	 * @property bool $can_sale Может продавать книги или нет
	 * @property int|null $profit_percent Процент от прибыли, который получает автор
	 * @property bool $disable_editing_for_co_author Запрет редактирования для соавторов
	 * @property-read \App\User|null $check_user
	 * @property-read \App\User $create_user
	 * @property-read mixed $is_accepted
	 * @property-read mixed $is_private
	 * @property-read mixed $is_rejected
	 * @property-read mixed $is_review_starts
	 * @property-read mixed $is_sent_for_review
	 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $manageable
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AuthorSaleRequest[] $saleRequests
	 * @property-read \App\User|null $status_changed_user
	 * @property-read \App\User|null $user
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager accepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager acceptedAndSentForReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager acceptedAndSentForReviewOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager acceptedAndSentForReviewOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager acceptedOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager acceptedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager authors()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager checked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager checkedAndOnCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager checkedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager editors()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager onCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager onlyChecked()
	 * @method static \Illuminate\Database\Query\Builder|Manager onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager orderStatusChangedAsc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager orderStatusChangedDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager private ()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager sentOnReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager sentOnReviewAndManageableNotPrivateAndNotOnReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager unaccepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager unchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereAcceptedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereAddTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereCanSale($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereCharacter($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereCheckUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereComment($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereDisableEditingForCoAuthor($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereHide($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereHideTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereHideUser($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereManageableId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereManageableType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereProfitPercent($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereRejectedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereSentForReviewAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereStatusChangedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereStatusChangedUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereStatusIn($statuses)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereStatusNot($status)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager whereUserId($value)
	 * @method static \Illuminate\Database\Query\Builder|Manager withTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager withUnchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Manager withoutCheckedScope()
	 * @method static \Illuminate\Database\Query\Builder|Manager withoutTrashed()
	 */
	class Manager extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Message
	 *
	 * @property int $id
	 * @property bool $is_read
	 * @property bool|null $recepient_del
	 * @property bool|null $sender_del
	 * @property int|null $recepient_id
	 * @property int $create_user_id
	 * @property string $text
	 * @property int $create_time
	 * @property bool $is_spam
	 * @property string|null $bb_text
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property bool $external_images_downloaded
	 * @property int $new Если 1, то сообщение новое (не прочитано), если 0, то прочитано
	 * @property bool $image_size_defined
	 * @property int|null $conversation_id
	 * @property string|null $deleted_at_for_created_user
	 * @property \Illuminate\Support\Carbon|null $user_updated_at
	 * @property-read \App\Conversation|null $conversation
	 * @property-read \App\User $create_user
	 * @property-read \App\User|null $recepient
	 * @property-read \App\User|null $sender
	 * @property-write mixed $b_b_text
	 * @property-read \App\User|null $user
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\MessageDelete[] $user_deletetions
	 * @method static \Illuminate\Database\Eloquent\Builder|Message joinUserDeletions($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message latestWithId($column = 'created_at')
	 * @method static \Illuminate\Database\Eloquent\Builder|Message messageNobodyRemoved()
	 * @method static \Illuminate\Database\Eloquent\Builder|Message messageNotReaded()
	 * @method static \Illuminate\Database\Eloquent\Builder|Message messageReaded()
	 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Message notDeletedForUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message oldestWithId($column = 'created_at')
	 * @method static \Illuminate\Database\Query\Builder|Message onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Message recepientRemove()
	 * @method static \Illuminate\Database\Eloquent\Builder|Message reciviedByUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message sendedByUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message senderRemove()
	 * @method static \Illuminate\Database\Eloquent\Builder|Message void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereBbText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereConversationId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreateTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereDeletedAtForCreatedUser($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereExternalImagesDownloaded($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereImageSizeDefined($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereIsRead($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereIsSpam($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereNew($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereRecepientDel($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereRecepientId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereSenderDel($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUserUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Message withDeletedForUser($user)
	 * @method static \Illuminate\Database\Query\Builder|Message withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|Message withoutTrashed()
	 */
	class Message extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\MessageDelete
	 *
	 * @property int $message_id
	 * @property int $user_id
	 * @property string $deleted_at
	 * @property-read \App\Message $message
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|MessageDelete newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|MessageDelete newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|MessageDelete query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|MessageDelete whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|MessageDelete whereMessageId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|MessageDelete whereUserId($value)
	 */
	class MessageDelete extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\ModeratorRequest
	 *
	 * @property int $id
	 * @property int $author_id
	 * @property int $user_id
	 * @property string $type
	 * @property string $text
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property string|null $deleted_at
	 * @property string $checked_at
	 * @property-read \App\Author $author
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorRequest checked()
	 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorRequest newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorRequest newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorRequest query()
	 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorRequest unchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorRequest whereAuthorId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorRequest whereCheckedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorRequest whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorRequest whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorRequest whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorRequest whereText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorRequest whereType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorRequest whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorRequest whereUserId($value)
	 */
	class ModeratorRequest extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Page
	 *
	 * @property int $id
	 * @property int $section_id
	 * @property string $content
	 * @property int $page
	 * @property array|null $html_tags_ids Массив всех id html тегов, которые содержатся в тексте
	 * @property int $book_id
	 * @property int|null $character_count
	 * @property int|null $book_page Номер страницы с начала книги
	 * @property-read \App\Book $book
	 * @property-read mixed $content_handled
	 * @property-read mixed $content_handled_splited
	 * @property-read \App\Section $section
	 * @method static \Illuminate\Database\Eloquent\Builder|Page inLinksIdSections($array)
	 * @method static \Illuminate\Database\Eloquent\Builder|Page newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Page newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Page query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Page whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Page whereBookPage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Page whereCharacterCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Page whereContent($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Page whereHtmlTagsIds($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Page whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Page wherePage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Page whereSectionId($value)
	 */
	class Page extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Participation
	 *
	 * @property int $user_id
	 * @property int $conversation_id
	 * @property int $new_messages_count
	 * @property int|null $latest_seen_message_id
	 * @property int|null $latest_message_id
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property-read \App\Conversation $conversation
	 * @property-read \App\Message|null $latest_message
	 * @property-read \App\Message|null $latest_seen_message
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|Participation messagesExists()
	 * @method static \Illuminate\Database\Eloquent\Builder|Participation newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Participation newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Participation query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Participation whereConversationId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Participation whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Participation whereLatestMessageId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Participation whereLatestSeenMessageId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Participation whereNewMessagesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Participation whereUserId($value)
	 */
	class Participation extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\PasswordReset
	 *
	 * @property int $id
	 * @property int $user_id
	 * @property string $email
	 * @property string $token
	 * @property string|null $used_at
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset notUsed()
	 * @method static \Illuminate\Database\Query\Builder|PasswordReset onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset query()
	 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset token($s)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereEmail($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereToken($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereUsedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereUserId($value)
	 * @method static \Illuminate\Database\Query\Builder|PasswordReset withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|PasswordReset withoutTrashed()
	 */
	class PasswordReset extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\PaymentSystemComission
	 *
	 * @property int $id
	 * @property string $payment_aggregator
	 * @property string $payment_system_type
	 * @property int $transaction_type
	 * @property float $comission
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission deposit()
	 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission lowerComissionFirst()
	 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission paymentSystemType($number)
	 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission query()
	 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission unitPay()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission whereComission($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission whereInPaymentSystemType($array)
	 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission wherePaymentAggregator($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission wherePaymentSystemType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission whereTransactionType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission whereUpdatedAt($value)
	 */
	class PaymentSystemComission extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Post
	 *
	 * @property int $id
	 * @property int $topic_id
	 * @property string $bb_text
	 * @property string $html_text
	 * @property int $create_time
	 * @property int $create_user_id
	 * @property int $edit_time
	 * @property int|null $edit_user_id
	 * @property int $hide
	 * @property int $hide_time
	 * @property int $hide_user
	 * @property string|null $tree
	 * @property int $children_count
	 * @property string|null $complain_user_ids
	 * @property int $checked
	 * @property int $like_count
	 * @property string|null $ip
	 * @property int $_lft
	 * @property int $_rgt
	 * @property int|null $parent_id
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property string|null $accepted_at
	 * @property int|null $forum_id
	 * @property \Illuminate\Support\Carbon|null $user_edited_at Время когда пользователь отредактировал
	 * @property string|null $sent_for_review_at
	 * @property bool $private
	 * @property int $level
	 * @property bool $external_images_downloaded
	 * @property int|null $status
	 * @property string|null $status_changed_at
	 * @property int|null $status_changed_user_id
	 * @property bool $image_size_defined
	 * @property int|null $user_agent_id
	 * @property string|null $rejected_at
	 * @property int|null $characters_count
	 * @property-read \App\Like|null $authUserLike
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Complain[] $complaints
	 * @property-read \App\User $create_user
	 * @property-read \App\User|null $edit_user
	 * @property-read \App\Forum|null $forum
	 * @property-read mixed $is_accepted
	 * @property-read mixed $is_private
	 * @property-read mixed $is_rejected
	 * @property-read mixed $is_review_starts
	 * @property-read mixed $is_sent_for_review
	 * @property-read mixed $level_with_limit
	 * @property Post|null $parent
	 * @property-read mixed $root
	 * @property-read mixed $text
	 * @property-read mixed $tree_array
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Like[] $likes
	 * @property-write mixed $b_b_text
	 * @property-read \App\User|null $status_changed_user
	 * @property-read \App\Topic $topic
	 * @property-read \App\UserAgent|null $user_agent
	 * @method static \Illuminate\Database\Eloquent\Builder|Post accepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post acceptedAndSentForReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post acceptedAndSentForReviewOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post acceptedAndSentForReviewOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post acceptedOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post acceptedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post any()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post checked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post checkedAndOnCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post checkedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post childs($ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post descendants($ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post fulltextSearch($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post latestWithId($column = 'created_at')
	 * @method static \Illuminate\Database\Eloquent\Builder|Post newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post oldestWithId($column = 'created_at')
	 * @method static \Illuminate\Database\Eloquent\Builder|Post onCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post onlyChecked()
	 * @method static \Illuminate\Database\Query\Builder|Post onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post orDescendants($ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Post orderStatusChangedAsc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post orderStatusChangedDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post private ()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post roots()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post sentOnReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post unaccepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post unchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereAcceptedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereBbText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCharactersCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereChecked($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereChildrenCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereComplainUserIds($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreateTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereEditTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereEditUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereExternalImagesDownloaded($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereForumId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereHide($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereHideTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereHideUser($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereHtmlText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereImageSizeDefined($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereIp($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereLevel($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereLft($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereLikeCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereParentId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePrivate($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereRejectedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereRgt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereSentForReviewAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereStatusChangedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereStatusChangedUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereStatusIn($statuses)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereStatusNot($status)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereTopicId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereTree($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUserAgentId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUserEditedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|Post withTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post withUnchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post withUserAccessToForums()
	 * @method static \Illuminate\Database\Eloquent\Builder|Post withoutCheckedScope()
	 * @method static \Illuminate\Database\Query\Builder|Post withoutTrashed()
	 */
	class Post extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\PriceChangeLog
	 *
	 * @property int $id
	 * @property int $book_id ID книги
	 * @property float|null $price Цена
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @method static \Illuminate\Database\Eloquent\Builder|PriceChangeLog newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|PriceChangeLog newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|PriceChangeLog query()
	 * @method static \Illuminate\Database\Eloquent\Builder|PriceChangeLog whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|PriceChangeLog whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|PriceChangeLog whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|PriceChangeLog wherePrice($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|PriceChangeLog whereUpdatedAt($value)
	 */
	class PriceChangeLog extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\ReferredUser
	 *
	 * @property int $id
	 * @property int $referred_by_user_id
	 * @property int $referred_user_id
	 * @property int $comission_buy_book
	 * @property int $comission_sell_book
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property-read \App\User $referred_by_user
	 * @property-read \App\User $referred_user
	 * @method static \Illuminate\Database\Eloquent\Builder|ReferredUser newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|ReferredUser newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|ReferredUser query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|ReferredUser whereComissionBuyBook($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ReferredUser whereComissionSellBook($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ReferredUser whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ReferredUser whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ReferredUser whereReferredByUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ReferredUser whereReferredUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ReferredUser whereUpdatedAt($value)
	 */
	class ReferredUser extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\SearchQueriesLog
	 *
	 * @property int $id
	 * @property string $query_text
	 * @property int|null $user_id
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @method static \Illuminate\Database\Eloquent\Builder|SearchQueriesLog newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|SearchQueriesLog newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|SearchQueriesLog query()
	 * @method static \Illuminate\Database\Eloquent\Builder|SearchQueriesLog whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|SearchQueriesLog whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|SearchQueriesLog whereQueryText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|SearchQueriesLog whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|SearchQueriesLog whereUserId($value)
	 */
	class SearchQueriesLog extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Section
	 *
	 * @property int $id
	 * @property int $inner_id
	 * @property string $type
	 * @property int $book_id
	 * @property string $title
	 * @property string|null $content
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property int $_lft
	 * @property int $_rgt
	 * @property int|null $parent_id
	 * @property int|null $character_count
	 * @property \Illuminate\Support\Carbon|null $user_edited_at Время когда пользователь отредактировал
	 * @property array|null $parameters
	 * @property array|null $html_tags_ids Массив всех id html тегов, которые содержатся в тексте
	 * @property int $pages_count
	 * @property int $status Статус главы. Пока будут варианты опубликована и в личном доступе или черновик
	 * @property string|null $status_changed_at Дата изменения статуса
	 * @property int|null $status_changed_user_id Пользователь последний изменивший статус
	 * @property-read \App\Book $book
	 * @property-read \Kalnoy\Nestedset\Collection|Section[] $children
	 * @property-read \App\User $create_user
	 * @property mixed $characters_count
	 * @property-read mixed $is_accepted
	 * @property-read mixed $is_private
	 * @property-read mixed $is_rejected
	 * @property-read mixed $is_review_starts
	 * @property-read mixed $is_sent_for_review
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Page[] $pages
	 * @property-read Section|null $parent
	 * @property-write mixed $element_id
	 * @property-read \App\User|null $status_changed_user
	 * @method static \Illuminate\Database\Eloquent\Builder|Section accepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section acceptedAndSentForReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section acceptedAndSentForReviewOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section acceptedAndSentForReviewOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section acceptedOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section acceptedOrBelongsToUser($user)
	 * @method static \Kalnoy\Nestedset\Collection|static[] all($columns = ['*'])
	 * @method static \Illuminate\Database\Eloquent\Builder|Section anchorSearch($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section any()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section chapter()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section chaptersOrNotes()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section checked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section checkedAndOnCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section checkedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section d()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section findInnerIdOrFail($innerId)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section fulltextSearch($searchText)
	 * @method static \Kalnoy\Nestedset\Collection|static[] get($columns = ['*'])
	 * @method static \Illuminate\Database\Eloquent\Builder|Section latestWithId($column = 'created_at')
	 * @method static \Kalnoy\Nestedset\QueryBuilder|Section newModelQuery()
	 * @method static \Kalnoy\Nestedset\QueryBuilder|Section newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section notes()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section oldestWithId($column = 'created_at')
	 * @method static \Illuminate\Database\Eloquent\Builder|Section onCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section onlyChecked()
	 * @method static \Illuminate\Database\Query\Builder|Section onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Section orderStatusChangedAsc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section orderStatusChangedDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section parametersIn($var, $array)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section private ()
	 * @method static \Kalnoy\Nestedset\QueryBuilder|Section query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section sentOnReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section unaccepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section unchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereCharacterCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereContent($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereHtmlTagsIds($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereInnerId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereLft($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section wherePagesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereParameters($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereParentId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereRgt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereStatusChangedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereStatusChangedUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereStatusIn($statuses)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereStatusNot($status)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereTitle($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Section whereUserEditedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|Section withTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section withUnchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Section withoutCheckedScope()
	 * @method static \Illuminate\Database\Query\Builder|Section withoutTrashed()
	 */
	class Section extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Sequence
	 *
	 * @property int $id
	 * @property string $name
	 * @property int|null $create_user_id
	 * @property int $hide
	 * @property int|null $merged_to
	 * @property int $book_count
	 * @property int $update_time
	 * @property int $hide_time
	 * @property int $hide_user
	 * @property string|null $hide_reason
	 * @property int $user_lib_count
	 * @property int $like_count
	 * @property string|null $description
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property \Illuminate\Support\Carbon|null $user_edited_at Время когда пользователь отредактировал
	 * @property string|null $accepted_at
	 * @property string|null $sent_for_review_at
	 * @property int|null $check_user_id
	 * @property int|null $status
	 * @property \Illuminate\Support\Carbon|null $status_changed_at
	 * @property int|null $status_changed_user_id
	 * @property int|null $merge_user_id
	 * @property \Illuminate\Support\Carbon|null $merged_at
	 * @property int|null $delete_user_id ID пользователя который удалил серию
	 * @property string|null $rejected_at
	 * @property int $added_to_favorites_count Количество пользователей добавивших в избранное
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Activity[] $activities
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $addedToFavoritesUsers
	 * @property-read \App\Like|null $authUserLike
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $books
	 * @property-read \App\User|null $create_user
	 * @property mixed $books_count
	 * @property-read mixed $is_accepted
	 * @property-read mixed $is_private
	 * @property-read mixed $is_rejected
	 * @property-read mixed $is_review_starts
	 * @property-read mixed $is_sent_for_review
	 * @property-read mixed $pivot_number
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserSequence[] $library_users
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Like[] $likes
	 * @property-read \App\User|null $merge_user
	 * @property-read Sequence|null $merged_sequence
	 * @property-read \App\User|null $status_changed_user
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence accepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence acceptedAndSentForReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence acceptedAndSentForReviewOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence acceptedAndSentForReviewOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence acceptedOrBelongsToAuthUser()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence acceptedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence any()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence checked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence checkedAndOnCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence checkedOrBelongsToUser($user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence fulltextSearch($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence notMerged()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence onCheck()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence onlyChecked()
	 * @method static \Illuminate\Database\Query\Builder|Sequence onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence orderByBooksCountAsc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence orderByBooksCountDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence orderStatusChangedAsc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence orderStatusChangedDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence private ()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence rememberCount($minutes = 5, $refresh = false)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence sentOnReview()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence similaritySearch($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence unaccepted()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence unchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereAcceptedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereAddedToFavoritesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereBookCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereCheckUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereDeleteUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereDescription($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereHide($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereHideReason($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereHideTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereHideUser($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereLikeCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereMergeUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereMergedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereMergedTo($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereRejectedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereSentForReviewAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereStatusChangedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereStatusChangedUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereStatusIn($statuses)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereStatusNot($status)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereUpdateTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereUserEditedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence whereUserLibCount($value)
	 * @method static \Illuminate\Database\Query\Builder|Sequence withTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence withUnchecked()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence withoutCheckedScope()
	 * @method static \Illuminate\Database\Query\Builder|Sequence withoutTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Sequence wordSimilaritySearch($searchText)
	 */
	class Sequence extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Setting
	 *
	 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 */
	class Setting extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Smile
	 *
	 * @property int $id
	 * @property string $name
	 * @property string $description
	 * @property string|null $simple_form
	 * @property string|null $for
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property object|null $parameters
	 * @property string $storage
	 * @property string|null $dirname
	 * @property-read mixed $full_url200x200
	 * @property-read mixed $full_url50x50
	 * @property-read mixed $full_url90x90
	 * @property-read mixed $full_url
	 * @property-read mixed $url
	 * @property-read mixed $full_url_sized
	 * @property-write mixed $max_height
	 * @property-write mixed $max_width
	 * @property-write mixed $quality
	 * @method static \Illuminate\Database\Eloquent\Builder|Smile considerTime()
	 * @method static \Illuminate\Database\Eloquent\Builder|Smile disableCache()
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Smile newModelQuery()
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Smile newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Smile newYear()
	 * @method static \Illuminate\Database\Query\Builder|Smile onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Smile query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Smile regular()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereDescription($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereDirname($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereFor($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereParameters($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereSimpleForm($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereStorage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Smile withCacheCooldownSeconds($seconds = null)
	 * @method static \Illuminate\Database\Query\Builder|Smile withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|Smile withoutTrashed()
	 */
	class Smile extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\TextBlock
	 *
	 * @property string $name
	 * @property string $text
	 * @property int $user_id
	 * @property int|null $time
	 * @property int $show_for_all
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int $id
	 * @property string|null $user_edited_at Время когда пользователь отредактировал
	 * @property-read \App\User $create_user
	 * @method static \Illuminate\Database\Eloquent\Builder|TextBlock name($name)
	 * @method static \Illuminate\Database\Eloquent\Builder|TextBlock newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|TextBlock newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|TextBlock query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|TextBlock whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|TextBlock whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|TextBlock whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|TextBlock whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|TextBlock whereShowForAll($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|TextBlock whereText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|TextBlock whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|TextBlock whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|TextBlock whereUserEditedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|TextBlock whereUserId($value)
	 */
	class TextBlock extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Topic
	 *
	 * @property int $id
	 * @property bool $closed
	 * @property int $forum_id
	 * @property string $name
	 * @property string|null $description
	 * @property int $create_time
	 * @property int $create_user_id
	 * @property int $post_count
	 * @property int $view_count
	 * @property int|null $last_post_id
	 * @property int $hide
	 * @property int $hide_time
	 * @property int $hide_user
	 * @property bool $post_desc
	 * @property int $main_priority
	 * @property bool $first_post_on_top
	 * @property int|null $top_post_id
	 * @property int $forum_priority
	 * @property bool $hide_from_main_page
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property string|null $user_edited_at Время когда пользователь отредактировал
	 * @property string|null $last_post_created_at
	 * @property bool $archived
	 * @property int|null $label
	 * @property-read \App\UserTopicSubscription|null $auth_user_subscription
	 * @property-read \App\User $create_user
	 * @property-read \App\Forum $forum
	 * @property-read \App\Post|null $last_post
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Post[] $posts
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Post[] $postsOrderedBySetting
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $subscribed_users
	 * @property-read \App\Post|null $top_post
	 * @property-read \App\UserTopicSubscription|null $user_subscriptions
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic any()
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic archived()
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic closed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic dontShowOnMainPage()
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic fulltextSearch($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic newQuery()
	 * @method static \Illuminate\Database\Query\Builder|Topic onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic opened()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic orderByLastPostAscNullsFirst()
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic orderByLastPostDescNullsLast()
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic orderByLastPostNullsLast()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic orderForIdeaForum()
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic public ()
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic trgmSearch($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic unarchived()
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereArchived($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereClosed($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereCreateTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereDescription($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereFirstPostOnTop($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereForumId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereForumPriority($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereHide($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereHideFromMainPage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereHideTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereHideUser($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereLabel($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereLastPostCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereLastPostId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereMainPriority($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic wherePostCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic wherePostDesc($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereTopPostId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereUserEditedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereViewCount($value)
	 * @method static \Illuminate\Database\Query\Builder|Topic withTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Topic withUserAccessToForums()
	 * @method static \Illuminate\Database\Query\Builder|Topic withoutTrashed()
	 */
	class Topic extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UrlShort
	 *
	 * @property int $id
	 * @property string $key Уникальный ключ
	 * @property string $url Ссылка на которую происходит переход
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @method static \Illuminate\Database\Eloquent\Builder|UrlShort newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UrlShort newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UrlShort query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UrlShort whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UrlShort whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UrlShort whereKey($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UrlShort whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UrlShort whereUrl($value)
	 */
	class UrlShort extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\User
	 *
	 * @property int $id
	 * @property int $user_group_id
	 * @property int $ec
	 * @property string $email
	 * @property string|null $nick
	 * @property \Illuminate\Support\Carbon $last_activity
	 * @property string|null $last_name
	 * @property string|null $first_name
	 * @property string|null $middle_name
	 * @property string|null $photo
	 * @property string $password
	 * @property int $gender
	 * @property \Illuminate\Support\Carbon $reg_date
	 * @property int $new_message_count
	 * @property string|null $reg_ip_old
	 * @property string|null $permission
	 * @property string|null $read_style
	 * @property int $mail_notif
	 * @property int $version
	 * @property int $comment_count
	 * @property int $user_lib_author_count
	 * @property int $user_lib_book_count
	 * @property int $user_lib_sequence_count
	 * @property int $forum_message_count
	 * @property \Illuminate\Support\Carbon|null $born_date
	 * @property int $born_date_show
	 * @property int $book_rate_count
	 * @property int $book_read_count
	 * @property int $book_read_later_count
	 * @property int $book_read_now_count
	 * @property string|null $city
	 * @property int $name_show_type
	 * @property int $book_read_not_complete_count
	 * @property int $hide
	 * @property \Illuminate\Support\Carbon $hide_time
	 * @property int $hide_user
	 * @property int $book_file_count
	 * @property int $profile_comment_count
	 * @property int $subscriptions_count
	 * @property int $subscribers_count
	 * @property int $friends_count
	 * @property int $blacklists_count
	 * @property int $hide_email
	 * @property int $invite_send
	 * @property int $book_read_not_read_count
	 * @property string|null $text_status
	 * @property int|null $avatar_id
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property \Illuminate\Support\Carbon|null $last_activity_at
	 * @property string|null $suspended_at
	 * @property int $photos_count
	 * @property string|null $user_edited_at Время когда пользователь отредактировал
	 * @property string|null $url_address
	 * @property int $topics_count Количество тем созданных пользователем
	 * @property int $confirmed_mailbox_count Количество подтвержденных почтовых ящиков
	 * @property int $achievements_count
	 * @property string|null $name_helper Вспомогательный столбец для быстрого trgm поиска
	 * @property string $reg_ip
	 * @property int $admin_notes_count
	 * @property int|null $miniature_image_id
	 * @property float $balance Баланс пользователя в рублях
	 * @property int|null $referred_by_user_id ID пользователя по рекомендации которого зарегистрировался этот пользователь
	 * @property bool|null $refresh_counters
	 * @property-read \App\UserAccountPermission $account_permissions
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Activity[] $actions
	 * @property-read \App\AdminNote|null $admin_note
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AdminNote[] $admin_notes
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserAuthFail[] $auth_fails
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserAuthLog[] $auth_logs
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AuthorStatus[] $author_read_statuses
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[] $authors
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[] $authors_read_statuses
	 * @property-read \App\UserPhoto|null $avatar
	 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $blacklists
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Blog[] $blog
	 * @property-read \App\Book|null $book
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $bookThatRated
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookStatus[] $book_read_statuses
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookmarkFolder[] $bookmark_folders
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Bookmark[] $bookmarks
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $books
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserSearchSetting[] $booksSearchSettings
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $books_read_statuses
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AuthorRepeat[] $created_author_repeats
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Author[] $created_authors
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookFile[] $created_book_files
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $created_books
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Collection[] $created_collections
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Forum[] $created_forums
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Keyword[] $created_keywords
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Sequence[] $created_sequences
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Topic[] $created_topics
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Blog[] $created_wall_posts
	 * @property-read \App\UserData $data
	 * @property-read \App\UserEmailNotificationSetting $email_notification_setting
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserEmail[] $emails
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserSubscriptionsEventNotification[] $eventNotificationSubscriptions
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Collection[] $favorite_collections
	 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $friends
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Genre[] $genre_blacklist
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserGenreBlacklist[] $genres_blacklist
	 * @property-read mixed $born_date_format
	 * @property-read mixed $group
	 * @property-read mixed $name
	 * @property-read mixed $user_name
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserGroup[] $groups
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Image[] $images
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserIncomingPayment[] $incoming_payment
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AdminNote[] $latest_admin_notes
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AchievementUser[] $latest_user_achievements
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Like[] $likes
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Manager[] $managers
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Message[] $messages
	 * @property-read \App\Image|null $miniature
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserNote[] $notes
	 * @property-read \App\UserEmail|null $notice_email
	 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
	 * @property-read \App\UserOnModeration|null $on_moderate
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserOutgoingPayment[] $outgoing_payment
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Participation[] $participations
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\PasswordReset[] $password_resets
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserPaymentTransaction[] $payment_transactions
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserPhoto[] $photos
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Post[] $posts
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $purchased_books
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserPurchase[] $purchases
	 * @property-read \App\UserReadStyle $readStyle
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserMoneyTransfer[] $receiving
	 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $refered_users
	 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $referred_by_user
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserRelation[] $relationship
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserRelation[] $relationshipReverse
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookReadRememberPage[] $remembered_pages
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserPurchase[] $sales
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SearchQueriesLog[] $searchQueries
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Message[] $sent_messages
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Sequence[] $sequences
	 * @property-read \App\UserSetting $setting
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookSimilarVote[] $similar_book_votes
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserSocialAccount[] $social_accounts
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $sold_books
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserTopicSubscription[] $subscribed_topics
	 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $subscribers
	 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $subscriptions
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserSurvey[] $surveys
	 * @property-read \App\Bookmark|null $thisPageInBookmarks
	 * @property-read \App\UserToken|null $token
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Topic[] $topics
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserMoneyTransfer[] $transfers
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\AchievementUser[] $user_achievements
	 * @property-read \App\UserGroup $user_group
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookVote[] $votes
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserPaymentDetail[] $wallets
	 * @method static \Illuminate\Database\Eloquent\Builder|User active()
	 * @method static \Illuminate\Database\Eloquent\Builder|User any()
	 * @method static \Illuminate\Database\Eloquent\Builder|User female()
	 * @method static \Illuminate\Database\Eloquent\Builder|User fulltextSearch($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|User male()
	 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|User online()
	 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|User orderByPostsCountDesc()
	 * @method static \Illuminate\Database\Eloquent\Builder|User orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|User query()
	 * @method static \Illuminate\Database\Eloquent\Builder|User rememberCount($minutes = 5, $refresh = false)
	 * @method static \Illuminate\Database\Eloquent\Builder|User similaritySearch($searchText)
	 * @method static \Illuminate\Database\Eloquent\Builder|User void()
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereAchievementsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereAdminNotesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatarId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereBalance($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereBlacklistsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereBookFileCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereBookRateCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereBookReadCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereBookReadLaterCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereBookReadNotCompleteCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereBookReadNotReadCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereBookReadNowCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereBornDate($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereBornDateShow($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereCity($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereCommentCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereConfirmedMailboxCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereEc($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmpty($column)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereForumMessageCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereFriendsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereHide($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereHideEmail($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereHideTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereHideUser($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereInviteSend($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastActivity($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastActivityAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereMailNotif($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereMiddleName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereMiniatureImageId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereNameHelper($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereNameShowType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereNewMessageCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereNick($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereNickEquals($nick)
	 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User wherePermission($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoto($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhotosCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfileCommentCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereReadStyle($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereReferredByUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereRefreshCounters($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereRegDate($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereRegIp($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereRegIpOld($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereSubscribersCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereSubscriptionsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereSuspendedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereTextStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereTextStatusLike($text)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereTopicsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereUrlAddress($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserEditedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserGroupId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserLibAuthorCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserLibBookCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserLibSequenceCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|User whereVersion($value)
	 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
	 */
	class User extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserAccountPermission
	 *
	 * @property int $user_id
	 * @property int $write_on_the_wall
	 * @property int $comment_on_the_wall
	 * @property int $write_private_messages
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int $view_relations
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountPermission newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountPermission newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountPermission query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountPermission whereCommentOnTheWall($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountPermission whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountPermission whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountPermission whereUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountPermission whereViewRelations($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountPermission whereWriteOnTheWall($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAccountPermission whereWritePrivateMessages($value)
	 */
	class UserAccountPermission extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserAgent
	 *
	 * @property int $id
	 * @property string $value
	 * @property-read \hisorange\BrowserDetect\Result $parsed
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAgent newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAgent newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAgent query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAgent whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAgent whereValue($value)
	 */
	class UserAgent extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserAuthFail
	 *
	 * @property int $id
	 * @property int $user_id
	 * @property string|null $password
	 * @property string $ip
	 * @property int $time
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int|null $user_agent_id
	 * @property-read \App\UserAgent|null $user_agent
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthFail newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthFail newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthFail query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthFail whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthFail whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthFail whereIp($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthFail wherePassword($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthFail whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthFail whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthFail whereUserAgentId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthFail whereUserId($value)
	 */
	class UserAuthFail extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserAuthLog
	 *
	 * @property int $id
	 * @property int $user_id
	 * @property string $ip
	 * @property int $time
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int|null $user_agent_id
	 * @property-read \App\User $user
	 * @property-read \App\UserAgent|null $user_agent
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthLog newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthLog newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthLog query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthLog whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthLog whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthLog whereIp($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthLog whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthLog whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthLog whereUserAgentId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthLog whereUserId($value)
	 */
	class UserAuthLog extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserAuthor
	 *
	 * @property int $user_id
	 * @property int $author_id
	 * @property int $time
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int $id
	 * @property-read \App\Author|null $author
	 * @property-read \App\User|null $user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthor newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthor newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthor query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthor whereAuthorId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthor whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthor whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthor whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthor whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserAuthor whereUserId($value)
	 */
	class UserAuthor extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserBook
	 *
	 * @property int $user_id
	 * @property int $book_id
	 * @property int $time
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int $id
	 * @property-read \App\Book|null $book
	 * @property-read \App\User|null $user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserBook newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserBook newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserBook query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserBook whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserBook whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserBook whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserBook whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserBook whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserBook whereUserId($value)
	 */
	class UserBook extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserData
	 *
	 * @property int $user_id
	 * @property string|null $favorite_authors
	 * @property string|null $favorite_genres
	 * @property string|null $favorite_music
	 * @property string|null $i_love
	 * @property string|null $i_hate
	 * @property string|null $about_self
	 * @property string|null $favorite_quote
	 * @property int|null $book_added_comment_count
	 * @property int|null $blog_record_comment_count
	 * @property string|null $last_ip
	 * @property int $old_friends_news_last_time_watch
	 * @property int|null $time_edit_profile
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int $password_reset_count
	 * @property string|null $last_time_password_is_reset
	 * @property \Illuminate\Support\Carbon|null $last_news_view_at
	 * @property int $created_books_count
	 * @property int $created_authors_count
	 * @property int $created_sequences_count
	 * @property \Illuminate\Support\Carbon|null $favorite_authors_books_latest_viewed_at
	 * @property int $books_purchased_count Количество книг купленных пользователем
	 * @property int|null $refer_users_count Количество привлеченных пользователей
	 * @property int|null $favorite_collections_count Количество избранных подборок
	 * @property int|null $created_collections_count Количество созданных подборок
	 * @property bool $invitation_to_take_survey_has_been_sent
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData newQuery()
	 * @method static \Illuminate\Database\Query\Builder|UserData onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereAboutSelf($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereBlogRecordCommentCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereBookAddedCommentCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereBooksPurchasedCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereCreatedAuthorsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereCreatedBooksCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereCreatedCollectionsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereCreatedSequencesCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereFavoriteAuthors($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereFavoriteAuthorsBooksLatestViewedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereFavoriteCollectionsCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereFavoriteGenres($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereFavoriteMusic($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereFavoriteQuote($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereIHate($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereILove($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereInvitationToTakeSurveyHasBeenSent($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereLastIp($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereLastNewsViewAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereLastTimePasswordIsReset($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereOldFriendsNewsLastTimeWatch($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData wherePasswordResetCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereReferUsersCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereTimeEditProfile($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereUserId($value)
	 * @method static \Illuminate\Database\Query\Builder|UserData withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|UserData withoutTrashed()
	 */
	class UserData extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserEmail
	 *
	 * @property int $id
	 * @property int $user_id
	 * @property string $email
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property bool $confirm
	 * @property string|null $deleted_at
	 * @property bool $show_in_profile
	 * @property bool $rescue
	 * @property bool $notice
	 * @property string|null $domain
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\UserEmailToken[] $tokens
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail confirmed()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail confirmedOrUnconfirmed()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail createdBeforeMoveToNewEngine()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail email($email)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail notNoticed()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail notice()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail query()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail rescuing()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail showedInProfile()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail unconfirmed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail whereConfirm($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail whereDomain($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail whereEmail($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail whereInEmails($emails)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail whereNotice($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail whereRescue($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail whereShowInProfile($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmail whereUserId($value)
	 */
	class UserEmail extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserEmailNotificationSetting
	 *
	 * @property int $user_id
	 * @property bool $news Когда появляется новость от администрации
	 * @property bool $private_message Когда приходит личное сообщение
	 * @property bool $forum_reply Когда приходит ответ на сообщение на форуме
	 * @property bool $wall_message Когда появляется новое сообщение на стене
	 * @property bool $comment_reply Когда кто-то отвечает на комментарий
	 * @property bool $wall_reply Когда кто-то отвечает на мое сообщение на стене
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property bool $db_forum_reply
	 * @property bool $db_wall_message
	 * @property bool $db_comment_reply
	 * @property bool $db_wall_reply
	 * @property bool $db_book_finish_parse
	 * @property bool $db_like
	 * @property bool $db_comment_vote_up
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting whereCommentReply($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting whereDbBookFinishParse($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting whereDbCommentReply($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting whereDbCommentVoteUp($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting whereDbForumReply($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting whereDbLike($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting whereDbWallMessage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting whereDbWallReply($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting whereForumReply($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting whereNews($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting wherePrivateMessage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting whereUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting whereWallMessage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailNotificationSetting whereWallReply($value)
	 */
	class UserEmailNotificationSetting extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserEmailToken
	 *
	 * @property int $user_email_id
	 * @property string $token
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int $id
	 * @property-read \App\UserEmail $email
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken newQuery()
	 * @method static \Illuminate\Database\Query\Builder|UserEmailToken onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken whereToken($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserEmailToken whereUserEmailId($value)
	 * @method static \Illuminate\Database\Query\Builder|UserEmailToken withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|UserEmailToken withoutTrashed()
	 */
	class UserEmailToken extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserFavoriteCollection
	 *
	 * @property int $id
	 * @property int $collection_id ID подборки
	 * @property int $user_id ID пользователя
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property-read \App\Collection $collection
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserFavoriteCollection newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserFavoriteCollection newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserFavoriteCollection query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserFavoriteCollection whereCollectionId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserFavoriteCollection whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserFavoriteCollection whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserFavoriteCollection whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserFavoriteCollection whereUserId($value)
	 */
	class UserFavoriteCollection extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserGenreBlacklist
	 *
	 * @property int $user_id
	 * @property int $genre_id
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGenreBlacklist newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGenreBlacklist newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGenreBlacklist query()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGenreBlacklist whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGenreBlacklist whereGenreId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGenreBlacklist whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGenreBlacklist whereUserId($value)
	 */
	class UserGenreBlacklist extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserGroup
	 *
	 * @property string $name
	 * @property string|null $permissions
	 * @property int $id
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property bool $not_show_ad
	 * @property bool $change_users_group
	 * @property bool $manage_users_groups
	 * @property bool $user_moderate
	 * @property bool $user_delete
	 * @property bool $user_suspend
	 * @property bool $add_comment
	 * @property bool $comment_self_edit_only_time
	 * @property bool $comment_edit_my
	 * @property bool $comment_edit_other_user
	 * @property bool $delete_my_comment
	 * @property bool $delete_other_user_comment
	 * @property bool $comment_view_who_likes_or_dislikes
	 * @property bool $add_book
	 * @property bool $add_book_without_check
	 * @property bool $edit_self_book
	 * @property bool $edit_other_user_book
	 * @property bool $author_edit
	 * @property bool $delete_hide_author
	 * @property bool $sequence_delete
	 * @property bool $sequence_edit
	 * @property bool $sequence_merge
	 * @property bool $send_message
	 * @property bool $delete_message
	 * @property bool $delete_self_book
	 * @property bool $delete_other_user_book
	 * @property bool $check_books
	 * @property bool $connect_books
	 * @property bool $author_repeat_report_add
	 * @property bool $author_repeat_report_delete
	 * @property bool $author_repeat_report_edit
	 * @property bool $merge_authors
	 * @property bool $forum_group_handle
	 * @property bool $add_forum_forum
	 * @property bool $forum_edit_forum
	 * @property bool $delete_forum_forum
	 * @property bool $forum_list_manipulate
	 * @property bool $add_forum_topic
	 * @property bool $delete_forum_self_topic
	 * @property bool $delete_forum_other_user_topic
	 * @property bool $edit_forum_self_topic
	 * @property bool $edit_forum_other_user_topic
	 * @property bool $manipulate_topic
	 * @property bool $add_forum_post
	 * @property bool $forum_edit_self_post_only_time
	 * @property bool $forum_edit_self_post
	 * @property bool $forum_edit_other_user_post
	 * @property bool $forum_delete_self_post
	 * @property bool $forum_delete_other_user_post
	 * @property bool $forum_topic_merge
	 * @property bool $forum_move_topic
	 * @property bool $forum_move_post
	 * @property bool $forum_post_manage
	 * @property bool $blog
	 * @property bool $blog_other_user
	 * @property bool $moderator_add_remove
	 * @property bool $author_editor_request
	 * @property bool $author_editor_check
	 * @property bool $vote_for_book
	 * @property bool $book_rate_other_user_remove
	 * @property bool $book_secret_hide_set
	 * @property bool $book_file_add
	 * @property bool $book_file_add_without_check
	 * @property bool $book_file_add_to_self_book_without_check
	 * @property bool $book_file_add_check
	 * @property bool $book_file_delete
	 * @property bool $book_keyword_add
	 * @property bool $book_keyword_add_new_with_check Добавлять новые ключевые слова с проверкой
	 * @property bool $book_keyword_remove
	 * @property bool $book_keyword_edit
	 * @property bool $book_keyword_moderate
	 * @property bool $book_keyword_vote
	 * @property bool $book_fb2_file_convert_divide_on_page
	 * @property bool $comment_add_vote
	 * @property bool $book_similar_vote
	 * @property bool $genre_add
	 * @property bool $like_click
	 * @property bool $edit_profile
	 * @property bool $edit_other_profile
	 * @property bool $add_genre_to_blacklist
	 * @property bool $author_group_and_ungroup
	 * @property bool $book_comments_manage
	 * @property bool $text_block
	 * @property bool $admin_comment
	 * @property bool $complain
	 * @property bool $complain_check
	 * @property bool $check_post_comments
	 * @property bool $access_to_closed_books
	 * @property bool $admin_panel_access
	 * @property bool $retry_failed_book_parse
	 * @property bool $achievement
	 * @property bool $watch_activity_logs
	 * @property bool $display_technical_information
	 * @property bool $refresh_counters
	 * @property bool $awards Создавать, редактировать, удалять награды
	 * @property bool $access_send_private_messages_avoid_privacy_and_blacklists
	 * @property bool $book_file_edit
	 * @property bool $shop_enable Включить функции магазина
	 * @property bool $author_sale_request_review Разрешение просматривать и проверять заявки авторов на продажи книг
	 * @property bool $withdrawal Может ли пользователь заказывать выплаты
	 * @property bool $transfer_money Может ли пользователь отправлять выплаты другим пользователям
	 * @property bool $view_financial_statistics Может ли пользователь просмотреть финансовую статистику сайта
	 * @property bool $show Отображать ли группу пользователей в списке
	 * @property bool $notify_assignment Уведомлять ли пользователя при присвоении группы
	 * @property bool $manage_collections Создавать, удалять, редактировать подборки. Добавлять книги в подборки
	 * @property bool $see_deleted Может ли пользователь видеть описания удаленных книг
	 * @property bool $edit_field_of_public_domain Может ли пользователь редактировать год перехода и метку книги общественнго достояния
	 * @property bool $enable_disable_changes_in_book Может ли пользователь запрещать/разрешать вносить изменения в книгу
	 * @property bool $edit_other_user_collections Может ли пользователь редактировать год перехода и метку книги общественнго достояния
	 * @property bool $manage_mailings Управлять рассылками
	 * @property bool $create_text_processing_books Добавлять книги
	 * @property string|null $key Код группы
	 * @property bool $view_user_surveys Просматривать опросы пользователей
	 * @property bool $deleting_online_read_and_files Может ли пользователь использовать функцию: "удалить все файлы, главы книги и изображения (кроме обложки)"
	 * @property-read mixed $rules
	 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup disableCache()
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|UserGroup newModelQuery()
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|UserGroup newQuery()
	 * @method static \Illuminate\Database\Query\Builder|UserGroup onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|UserGroup query()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup show()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAccessSendPrivateMessagesAvoidPrivacyAndBlacklists($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAccessToClosedBooks($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAchievement($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAddBook($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAddBookWithoutCheck($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAddComment($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAddForumForum($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAddForumPost($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAddForumTopic($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAddGenreToBlacklist($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAdminComment($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAdminPanelAccess($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAuthorEdit($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAuthorEditorCheck($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAuthorEditorRequest($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAuthorGroupAndUngroup($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAuthorRepeatReportAdd($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAuthorRepeatReportDelete($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAuthorRepeatReportEdit($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAuthorSaleRequestReview($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereAwards($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBlog($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBlogOtherUser($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBookCommentsManage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBookFb2FileConvertDivideOnPage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBookFileAdd($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBookFileAddCheck($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBookFileAddToSelfBookWithoutCheck($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBookFileAddWithoutCheck($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBookFileDelete($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBookFileEdit($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBookKeywordAdd($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBookKeywordAddNewWithCheck($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBookKeywordEdit($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBookKeywordModerate($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBookKeywordRemove($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBookKeywordVote($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBookRateOtherUserRemove($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBookSecretHideSet($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereBookSimilarVote($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereChangeUsersGroup($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereCheckBooks($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereCheckPostComments($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereCommentAddVote($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereCommentEditMy($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereCommentEditOtherUser($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereCommentSelfEditOnlyTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereCommentViewWhoLikesOrDislikes($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereComplain($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereComplainCheck($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereConnectBooks($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereCreateTextProcessingBooks($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereDeleteForumForum($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereDeleteForumOtherUserTopic($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereDeleteForumSelfTopic($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereDeleteHideAuthor($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereDeleteMessage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereDeleteMyComment($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereDeleteOtherUserBook($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereDeleteOtherUserComment($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereDeleteSelfBook($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereDeletingOnlineReadAndFiles($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereDisplayTechnicalInformation($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereEditFieldOfPublicDomain($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereEditForumOtherUserTopic($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereEditForumSelfTopic($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereEditOtherProfile($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereEditOtherUserBook($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereEditOtherUserCollections($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereEditProfile($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereEditSelfBook($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereEnableDisableChangesInBook($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereForumDeleteOtherUserPost($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereForumDeleteSelfPost($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereForumEditForum($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereForumEditOtherUserPost($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereForumEditSelfPost($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereForumEditSelfPostOnlyTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereForumGroupHandle($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereForumListManipulate($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereForumMovePost($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereForumMoveTopic($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereForumPostManage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereForumTopicMerge($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereGenreAdd($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereKey($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereLikeClick($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereManageCollections($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereManageMailings($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereManageUsersGroups($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereManipulateTopic($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereMergeAuthors($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereModeratorAddRemove($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereNotShowAd($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereNotifyAssignment($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup wherePermissions($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereRefreshCounters($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereRetryFailedBookParse($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereSeeDeleted($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereSendMessage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereSequenceDelete($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereSequenceEdit($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereSequenceMerge($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereShopEnable($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereShow($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereTextBlock($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereTransferMoney($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereUserDelete($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereUserModerate($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereUserSuspend($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereViewFinancialStatistics($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereViewUserSurveys($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereVoteForBook($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereWatchActivityLogs($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup whereWithdrawal($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup withCacheCooldownSeconds($seconds = null)
	 * @method static \Illuminate\Database\Query\Builder|UserGroup withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|UserGroup withoutTrashed()
	 */
	class UserGroup extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserGroupPivot
	 *
	 * @property int $user_id
	 * @property int $user_group_id
	 * @property \Illuminate\Support\Carbon|null $created_at Время создания данных
	 * @property \Illuminate\Support\Carbon|null $updated_at Время обновления данных
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroupPivot newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroupPivot newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroupPivot query()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroupPivot whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroupPivot whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroupPivot whereUserGroupId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserGroupPivot whereUserId($value)
	 */
	class UserGroupPivot extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserIncomingPayment
	 *
	 * @property int $id
	 * @property string $payment_type Код платежной системы
	 * @property int $user_id Аккаунт пользователя на который зачисляется платеж
	 * @property string $ip IP с которого осуществляется платеж
	 * @property string $currency Код валюты
	 * @property int|null $payment_id ID транзакции внутри платежного агрегатора
	 * @property string $payment_aggregator Название платежного агрегатора приема платежей
	 * @property object|null $params Все данные платежа
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property-read \App\UserPaymentTransaction|null $transaction
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment newQuery()
	 * @method static \Illuminate\Database\Query\Builder|UserIncomingPayment onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment query()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment unitPay()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment unitPayPayment($id)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment whereCurrency($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment whereIp($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment whereParams($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment wherePaymentAggregator($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment wherePaymentId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment wherePaymentType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment whereUserId($value)
	 * @method static \Illuminate\Database\Query\Builder|UserIncomingPayment withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|UserIncomingPayment withoutTrashed()
	 */
	class UserIncomingPayment extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserMoneyTransfer
	 *
	 * @property int $id
	 * @property int $sender_user_id
	 * @property int $recepient_user_id
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property-read \App\User $recepient
	 * @property-read \App\UserPaymentTransaction|null $recepient_transaction
	 * @property-read \App\User $sender
	 * @property-read \App\UserPaymentTransaction|null $sender_transaction
	 * @property-read \App\UserPaymentTransaction|null $transaction
	 * @method static \Illuminate\Database\Eloquent\Builder|UserMoneyTransfer newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserMoneyTransfer newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserMoneyTransfer query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserMoneyTransfer whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserMoneyTransfer whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserMoneyTransfer whereRecepientUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserMoneyTransfer whereSenderUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserMoneyTransfer whereUpdatedAt($value)
	 */
	class UserMoneyTransfer extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserNote
	 *
	 * @property int $id
	 * @property int $create_user_id
	 * @property string $text
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property string $bb_text
	 * @property bool $external_images_downloaded
	 * @property-read \App\User $create_user
	 * @property-write mixed $b_b_text
	 * @method static \Illuminate\Database\Eloquent\Builder|UserNote any()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserNote newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserNote newQuery()
	 * @method static \Illuminate\Database\Query\Builder|UserNote onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserNote query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereBbText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereExternalImagesDownloaded($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereText($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|UserNote withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|UserNote withoutTrashed()
	 */
	class UserNote extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserOnModeration
	 *
	 * @property int $user_id
	 * @property int|null $time
	 * @property int $user_adds_id
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int $id
	 * @property-read \App\User|null $user
	 * @property-read \App\User|null $user_adds
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOnModeration newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOnModeration newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOnModeration query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOnModeration whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOnModeration whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOnModeration whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOnModeration whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOnModeration whereUserAddsId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOnModeration whereUserId($value)
	 */
	class UserOnModeration extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserOutgoingPayment
	 *
	 * @property int $id
	 * @property int $user_id Аккаунт пользователя c которого идет платеж
	 * @property string $ip IP пользователя, который заказывает выплату
	 * @property string $purse Номер кошелька на который перечисляется выплата
	 * @property string $payment_type Тип платежной системы на которую перечисляется выплата
	 * @property int $wallet_id ID кошелька для выплаты
	 * @property string|null $payment_aggregator Платежный агрегатор через который осуществляется выплата
	 * @property int|null $payment_aggregator_transaction_id ID транзакции платежного агрегатора, через который происходит выплата
	 * @property object|null $params Данные полученные от платежной системы
	 * @property int|null $retry_failed_count Сколько попыток было отправить платеж
	 * @property string|null $last_failed_retry_at Время последней попытки отправить платеж
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property string $uniqid Уникальный номер транзакции
	 * @property-read \App\UserPaymentTransaction|null $transaction
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment newQuery()
	 * @method static \Illuminate\Database\Query\Builder|UserOutgoingPayment onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereIp($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereLastFailedRetryAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereParams($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment wherePaymentAggregator($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment wherePaymentAggregatorTransactionId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment wherePaymentType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment wherePurse($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereRetryFailedCount($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereUniqid($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereWalletId($value)
	 * @method static \Illuminate\Database\Query\Builder|UserOutgoingPayment withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|UserOutgoingPayment withoutTrashed()
	 */
	class UserOutgoingPayment extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserPaymentDetail
	 *
	 * @property int $id
	 * @property int $user_id ID пользователя которму принадлежат платежные данные
	 * @property string $type Тип платежной системы
	 * @property string $number Номер кошелька или карты
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property object|null $params Дополнительная информация о платежных данных
	 * @property mixed $qiwi
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail newQuery()
	 * @method static \Illuminate\Database\Query\Builder|UserPaymentDetail onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail whereNumber($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail whereParams($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail whereType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail whereUserId($value)
	 * @method static \Illuminate\Database\Query\Builder|UserPaymentDetail withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|UserPaymentDetail withoutTrashed()
	 */
	class UserPaymentDetail extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserPaymentTransaction
	 *
	 * @property int $id
	 * @property float $sum Списание или пополненение на балансе
	 * @property int $user_id Аккаунт пользователя
	 * @property int $type Тип операции
	 * @property int $operable_type ID morph таблицы
	 * @property int $operable_id ID в таблице
	 * @property int $status Статус платежа
	 * @property \Illuminate\Support\Carbon $status_changed_at Дата изменения статуса платежа
	 * @property float|null $balance_before Баланс до проведения операции
	 * @property object|null $params Дополнительные данные
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property-read mixed $balance_after
	 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $operable
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction deposit()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction newQuery()
	 * @method static \Illuminate\Database\Query\Builder|UserPaymentTransaction onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction processed()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction wait()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereBalanceBefore($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereOperableId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereOperableType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereParams($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereStatusChangedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereSum($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereUserId($value)
	 * @method static \Illuminate\Database\Query\Builder|UserPaymentTransaction withTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction withdrawal()
	 * @method static \Illuminate\Database\Query\Builder|UserPaymentTransaction withoutTrashed()
	 */
	class UserPaymentTransaction extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserPhoto
	 *
	 * @property int $id
	 * @property int $user_id
	 * @property string $name
	 * @property int $size
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property object|null $parameters
	 * @property string $storage
	 * @property string|null $dirname
	 * @property string|null $md5
	 * @property-read \App\User $create_user
	 * @property-read mixed $full_url200x200
	 * @property-read mixed $full_url50x50
	 * @property-read mixed $full_url90x90
	 * @property-read mixed $full_url
	 * @property-read mixed $url
	 * @property-read mixed $full_url_sized
	 * @property-write mixed $max_height
	 * @property-write mixed $max_width
	 * @property-write mixed $path_to_file
	 * @property-write mixed $quality
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto newQuery()
	 * @method static \Illuminate\Database\Query\Builder|UserPhoto onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereDirname($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereMd5($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereParameters($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereSize($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereStorage($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereUserId($value)
	 * @method static \Illuminate\Database\Query\Builder|UserPhoto withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|UserPhoto withoutTrashed()
	 */
	class UserPhoto extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserPurchase
	 *
	 * @property int $id
	 * @property int $buyer_user_id Аккаунт пользователя, который оплачивает
	 * @property int $seller_user_id Аккаунт пользователя, который получает выплату
	 * @property string $purchasable_type Тип объекта за который происходит оплата
	 * @property int $purchasable_id ID объекта за который происходит оплата
	 * @property float $price Цена по которой куплен объект
	 * @property int $site_commission Комиссия сайта
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property \Illuminate\Support\Carbon|null $deleted_at
	 * @property \Illuminate\Support\Carbon|null $canceled_at Время отмены покупки
	 * @property-read \App\User $buyer
	 * @property-read \App\UserPaymentTransaction|null $buyer_transaction
	 * @property-read \App\UserPaymentTransaction|null $commission_transaction
	 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $purchasable
	 * @property-read \App\UserPaymentTransaction|null $referer_buyer_transaction
	 * @property-read \App\UserPaymentTransaction|null $referer_seller_transaction
	 * @property-read \App\User $seller
	 * @property-read \App\UserPaymentTransaction|null $seller_transaction
	 * @property-read \App\UserPaymentTransaction|null $transaction
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase notCanceled()
	 * @method static \Illuminate\Database\Query\Builder|UserPurchase onlyTrashed()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase whereBuyerUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase whereCanceledAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase whereDeletedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase wherePrice($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase wherePurchasableId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase wherePurchasableType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase whereSellerUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase whereSiteCommission($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Query\Builder|UserPurchase withTrashed()
	 * @method static \Illuminate\Database\Query\Builder|UserPurchase withoutTrashed()
	 */
	class UserPurchase extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserReadStyle
	 *
	 * @property int $user_id
	 * @property int|null $font
	 * @property int|null $align
	 * @property int|null $size
	 * @property string|null $background_color
	 * @property string|null $font_color
	 * @property string|null $card_color
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserReadStyle disableCache()
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|UserReadStyle newModelQuery()
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|UserReadStyle newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|UserReadStyle query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserReadStyle whereAlign($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserReadStyle whereBackgroundColor($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserReadStyle whereCardColor($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserReadStyle whereFont($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserReadStyle whereFontColor($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserReadStyle whereSize($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserReadStyle whereUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserReadStyle withCacheCooldownSeconds($seconds = null)
	 */
	class UserReadStyle extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserReference
	 *
	 * @property-read \App\User $refer_user
	 * @property-read \App\User $referred_user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserReference newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserReference newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserReference query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 */
	class UserReference extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserRelation
	 *
	 * @property int $user_id
	 * @property int $user_id2
	 * @property int|null $status
	 * @property int|null $time
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int $id
	 * @property string $user_updated_at
	 * @property-read \App\User|null $first_user
	 * @property-read \App\User|null $second_user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserRelation friends()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserRelation friendsAndSubscribers()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserRelation newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserRelation newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserRelation query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserRelation whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserRelation whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserRelation whereStatus($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserRelation whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserRelation whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserRelation whereUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserRelation whereUserId2($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserRelation whereUserUpdatedAt($value)
	 */
	class UserRelation extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserSearchSetting
	 *
	 * @property int $user_id
	 * @property string $name Название настройки
	 * @property string $value Значение настройки
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSearchSetting newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSearchSetting newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSearchSetting query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSearchSetting whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSearchSetting whereUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSearchSetting whereValue($value)
	 */
	class UserSearchSetting extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserSequence
	 *
	 * @property int $user_id
	 * @property int $sequence_id
	 * @property int $time
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int $id
	 * @property-read \App\Sequence $sequence
	 * @property-read \App\User|null $user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSequence newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSequence newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSequence query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSequence whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSequence whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSequence whereSequenceId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSequence whereTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSequence whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSequence whereUserId($value)
	 */
	class UserSequence extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserSetting
	 *
	 * @property int $user_id
	 * @property string|null $bookmark_folder_order
	 * @property string|null $email_delivery
	 * @property string|null $user_access
	 * @property string|null $genre_blacklist
	 * @property int|null $blog_top_record
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property array|null $permissions_to_act
	 * @property bool $login_with_id Можно ли использовать в качестве логина id
	 * @property int $font_size_px
	 * @property int|null $font_family
	 * @property-read \App\Blog|null $top_blog_record
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting disableCache()
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|UserSetting newModelQuery()
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|UserSetting newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|UserSetting query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereBlogTopRecord($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereBookmarkFolderOrder($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereEmailDelivery($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereFontFamily($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereFontSizePx($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereGenreBlacklist($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereLoginWithId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting wherePermissionsToAct($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereUserAccess($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting whereUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSetting withCacheCooldownSeconds($seconds = null)
	 */
	class UserSetting extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserSocialAccount
	 *
	 * @property int $id
	 * @property int $user_id
	 * @property string $provider_user_id
	 * @property string $provider
	 * @property string $access_token
	 * @property object $parameters
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSocialAccount newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSocialAccount newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSocialAccount query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSocialAccount whereAccessToken($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSocialAccount whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSocialAccount whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSocialAccount whereParameters($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSocialAccount whereProvider($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSocialAccount whereProviderUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSocialAccount whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSocialAccount whereUserId($value)
	 */
	class UserSocialAccount extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserSubscriptionsEventNotification
	 *
	 * @property int $id
	 * @property int $notifiable_user_id ID пользователя которому присылаются уведомления
	 * @property int $eventable_type Тип объекта при для которго появляется событие
	 * @property int $eventable_id ID объекта у которого появляется событие
	 * @property int $event_type Тип события
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $eventable
	 * @property-read \App\User $notifiable_user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification whereEventType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification whereEventableId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification whereEventableType($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification whereNotifiableUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification whereUpdatedAt($value)
	 */
	class UserSubscriptionsEventNotification extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserSurvey
	 *
	 * @property int $id
	 * @property int $create_user_id
	 * @property string|null $do_you_read_books_or_download_them Вы читаете книги или скачиваете?
	 * @property array|null $what_file_formats_do_you_download Если вы скачиваете книги, то какие форматы вы предпочитаете? (можно выбрать несколько вариантов)
	 * @property string|null $how_improve_download_book_files Напишите, как можно улучшить файлы книг
	 * @property int|null $how_do_you_rate_the_convenience_of_reading_books_online Если вы читаете книги онлайн, то как вы оцениваете удобство чтения книг онлайн?
	 * @property string|null $how_to_improve_the_convenience_of_reading_books_online Напишите, как можно улучшить удобство чтения книг онлайн
	 * @property int|null $how_do_you_rate_the_convenience_and_functionality_of_search Как вы оцениваете удобство и функциональность поиска книг, авторов?
	 * @property string|null $how_to_improve_the_convenience_of_searching_for_books Напишите, как можно улучшить удобство поиска книг и авторов
	 * @property int|null $how_do_you_rate_the_site_design Как вы оцениваете дизайн сайта?
	 * @property string|null $how_to_improve_the_site_design Напишите, как можно улучшить дизайн сайта
	 * @property int|null $how_do_you_assess_the_work_of_the_site_administration Как вы оцениваете работу администрации сайта?
	 * @property string|null $how_improve_the_site_administration Напишите, как можно улучшить работу администрации сайта
	 * @property string|null $what_do_you_like_on_the_site Напишите, что вам нравится на нашем сайте
	 * @property string|null $what_you_dont_like_about_the_site Напишите, что вам не нравится на нашем сайте
	 * @property string|null $what_you_need_on_our_site Напишите, чего вам не хватает на нашем сайте
	 * @property array|null $what_site_features_are_interesting_to_you Какие функции сайта вам интересны? (можно выбрать несколько вариантов)
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property-read \App\User $create_user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey query()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereCreateUserId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereCreator(\App\User $user)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereDoYouReadBooksOrDownloadThem($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowDoYouAssessTheWorkOfTheSiteAdministration($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowDoYouRateTheConvenienceAndFunctionalityOfSearch($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowDoYouRateTheConvenienceOfReadingBooksOnline($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowDoYouRateTheSiteDesign($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowImproveDownloadBookFiles($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowImproveTheSiteAdministration($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowToImproveTheConvenienceOfReadingBooksOnline($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowToImproveTheConvenienceOfSearchingForBooks($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereHowToImproveTheSiteDesign($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereWhatDoYouLikeOnTheSite($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereWhatFileFormatsDoYouDownload($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereWhatSiteFeaturesAreInterestingToYou($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereWhatYouDontLikeAboutTheSite($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserSurvey whereWhatYouNeedOnOurSite($value)
	 */
	class UserSurvey extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserToken
	 *
	 * @property int $user_id
	 * @property string $token
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserToken newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserToken newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserToken query()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereToken($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserToken whereUserId($value)
	 */
	class UserToken extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UserTopicSubscription
	 *
	 * @property int $id
	 * @property int $topic_id
	 * @property int $user_id
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property-read \App\Topic $topic
	 * @property-read \App\User $user
	 * @method static \Illuminate\Database\Eloquent\Builder|UserTopicSubscription newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserTopicSubscription newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UserTopicSubscription query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UserTopicSubscription whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserTopicSubscription whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserTopicSubscription whereTopicId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserTopicSubscription whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UserTopicSubscription whereUserId($value)
	 */
	class UserTopicSubscription extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\UsersAccessToForum
	 *
	 * @property int $user_id
	 * @property int $forum_id
	 * @method static \Illuminate\Database\Eloquent\Builder|UsersAccessToForum newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|UsersAccessToForum newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|UsersAccessToForum query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|UsersAccessToForum whereForumId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|UsersAccessToForum whereUserId($value)
	 */
	class UsersAccessToForum extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\Variable
	 *
	 * @property string $name
	 * @property string|null $value
	 * @property int $update_time
	 * @property \Illuminate\Support\Carbon|null $created_at
	 * @property \Illuminate\Support\Carbon|null $updated_at
	 * @property int $id
	 * @method static \Illuminate\Database\Eloquent\Builder|Variable disableCache()
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Variable newModelQuery()
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Variable newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|Variable query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|Variable whereCreatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Variable whereId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Variable whereName($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Variable whereUpdateTime($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Variable whereUpdatedAt($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Variable whereValue($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|Variable withCacheCooldownSeconds($seconds = null)
	 */
	class Variable extends \Eloquent
	{
	}
}

namespace App {
	/**
	 * App\ViewCount
	 *
	 * @property int $book_id
	 * @property int $all
	 * @property int $week
	 * @property int $year
	 * @property int $month
	 * @property int $day
	 * @property-read \App\Book $book
	 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount newModelQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount newQuery()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
	 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
	 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount query()
	 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
	 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount whereAll($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount whereBookId($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount whereDay($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount whereMonth($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount whereWeek($value)
	 * @method static \Illuminate\Database\Eloquent\Builder|ViewCount whereYear($value)
	 */
	class ViewCount extends \Eloquent
	{
	}
}

