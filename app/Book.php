<?php

namespace App;

use App\Enums\AuthorEnum;
use App\Enums\BookComplete;
use App\Enums\CacheTags;
use App\Enums\StatusEnum;
use App\IndexConfigurators\BookIndexConfigurator;
use App\Jobs\Author\UpdateAuthorBooksCount;
use App\Jobs\Book\BookUpdateCharactersCountJob;
use App\Jobs\Book\BookUpdatePageNumbersJob;
use App\Jobs\Book\UpdateBookAge;
use App\Jobs\Book\UpdateBookAttachmentsCount;
use App\Jobs\Book\UpdateBookFilesCount;
use App\Jobs\Book\UpdateBookNotesCount;
use App\Jobs\Book\UpdateBookPagesCount;
use App\Jobs\Book\UpdateBookSectionsCount;
use App\Jobs\Sequence\UpdateSequenceBooksCount;
use App\Jobs\UpdateGenreBooksCount;
use App\Library\BookSqlite;
use App\Library\Old\xsBookPath;
use App\Scopes\CheckedScope;
use App\Scopes\NotConnectedScope;
use App\Traits\AdminNoteableTrait;
use App\Traits\CheckedItems;
use App\Traits\Commentable;
use App\Traits\ComplainableTrait;
use App\Traits\FavoritableTrait;
use App\Traits\GroupTrait;
use App\Traits\Likeable;
use App\Traits\LogsActivity;
use App\Traits\ReadDownloadAccess;
use App\Traits\UserCreate;
use Auth;
use Cache;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


// use IgnorableObservers\IgnorableObservers;

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
 * @method static Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Book orderByRatingAsc()
 * @method static \Illuminate\Database\Eloquent\Builder|Book orderByRatingDayDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|Book orderByRatingDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|Book orderByRatingMonthDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|Book orderByRatingQuarterDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|Book orderByRatingWeekDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|Book orderByRatingYearDesc()
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
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
 * @mixin \Eloquent
 */
class Book extends Model
{
	//use Searchable;
	use SoftDeletes;
	use CheckedItems;
	use UserCreate;
	use LogsActivity;
	use ReadDownloadAccess;
	use GroupTrait;
	use AdminNoteableTrait;
	use PivotEventTrait;
	use Likeable;
	use ComplainableTrait;
	use FavoritableTrait;
	use Commentable;

	protected static $logAttributes = [
		'title',
		'is_si',
		'is_lp',
		'is_collection',
		'ti_lb',
		'ti_olb',
		'year_writing',
		'rightholder',
		'is_public',
		'year_public',
		'pi_bn',
		'pi_pub',
		'pi_city',
		'pi_year',
		'pi_isbn',
		'ready_status',
		'swear',
		'age',
		'images_exists',
		'copy_protection'
	];

	protected static $recordEvents = [];

	protected static $logOnlyDirty = true;

	protected static $submitEmptyLogs = false;

	public $visible = [
		'id',
		'title',
		'genres',
		'writers',
		'sequences',
		'book_keywords',
		'is_si',
		'is_lp',
		'is_collection',
		'ti_lb',
		'ti_olb',
		'year_writing',
		'rightholder',
		'is_public',
		'year_public',
		'pi_bn',
		'pi_pub',
		'pi_city',
		'pi_year',
		'pi_isbn',
		'ready_status',
		'swear',
		'age',
		'read_access',
		'download_access',
		'secret_hide_reason',
		'images_exists',
		'copy_protection',
		'create_user_id',
		'status',
		'status_changed_at',
		'created_at',
		'updated_at',
		'deleted_at',
		'like_count'
	];

	public $swearArray = [
		0 => 'null',
		1 => 'no',
		2 => 'yes'
	];

	protected $attributes =
		[
			'status' => StatusEnum::Private,
			'online_read_new_format' => true
		];

	// Here you can specify a mapping for a model fields.
	protected $indexConfigurator = BookIndexConfigurator::class;
	protected $searchRules = [
		//
	];
	protected $mapping = [
		'properties' => [
			'id' => [
				'type' => 'integer'
			],
			'title' => [
				'type' => 'text',
				'analyzer' => 'edge_ngram_analyzer',
				'search_analyzer' => 'keyword_analyzer',
			],
			'authors' => [
				'type' => 'text',
				'analyzer' => 'edge_ngram_analyzer',
				'search_analyzer' => 'keyword_analyzer',
			],
			'pi_isbn' => [
				'type' => 'text'
			],
			'create_user_id' => [
				'type' => 'integer'
			],
			'status' => [
				'type' => 'integer'
			],
		]
	];
	protected $guarded = ['id'];

	protected $fillable = [
		'title',
		'is_si',
		'is_lp',
		'is_collection',
		'ti_lb',
		'ti_olb',
		'year_writing',
		'rightholder',
		'is_public',
		'year_public',
		'pi_bn',
		'pi_pub',
		'pi_city',
		'pi_year',
		'pi_isbn',
		'ready_status',
		'swear',
		'age',
		'read_access',
		'download_access',
		'secret_hide_reason',
		'images_exists',
		'copy_protection'
	];

	protected $casts = [
		'formats' => 'array',
		'parse_errors' => 'array',
		'year_writing' => 'integer',
		'pi_year' => 'integer',
		'copy_protection' => 'boolean'
	];
	protected $dates = [
		'status_changed_at',
		'connected_at',
		'user_edited_at',
		'price_updated_at'
	];
	protected $perPage = 20;
	private $sqlite;

	const FAVORITABLE_PIVOT_TABLE = 'user_books';

	public static function boot()
	{
		parent::boot();

		//static::addGlobalScope(new CheckedScope);

		//static::addGlobalScope(new NotConnectedScope);

		static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttributes) {
			if ($relationName == 'genres') {
				foreach ($pivotIds as $id) {
					$model->attachToGenreHepler($id);
				}
			}
		});

		static::pivotDetached(function ($model, $relationName, $pivotIds) {
			if ($relationName == 'genres') {
				foreach ($pivotIds as $id) {
					$model->detachFromGenreHepler($id);
				}
			}
		});
	}

	static function getCachedOnModerationCount()
	{
		return Cache::tags([CacheTags::BooksOnModerationCount])->remember('count', 3600, function () {
			return self::sentOnReview()->count();
		});
	}

	static function flushCachedOnModerationCount()
	{
		Cache::tags([CacheTags::BooksOnModerationCount])->pull('count');
	}

	static function cachedCountRefresh()
	{
		Cache::forever('books_count_refresh', true);
	}

	public function searchableAs()
	{
		return 'book_index';
	}

	/**
	 * Get the indexable data array for the model.
	 *
	 * @return array
	 */
	public function toSearchableArray()
	{
		$array = $this->toArray();

		$array['authors'] = $this->writers()
			->get()
			->pluck('name')
			->toArray();

		return $array;
	}

	public function writers()
	{
		return $this->belongsToMany('App\Author', 'book_authors', 'book_id', 'author_id')
			->withPivot(['order', 'type'])
			->withPivotValue('type', AuthorEnum::Writer)
			->orderBy('order')
			->using(BookAuthor::class)
			->withTimestamps();
	}

	public function attachToGenreHepler($number)
	{
		$str = trim($this->genres_helper, '{}');

		$array = (array)array_filter(explode(',', $str));

		$array[] = (int)$number;

		$array = array_unique($array);

		$this->genres_helper = '{' . implode(',', $array) . '}';
	}

	public function detachFromGenreHepler($number)
	{
		$str = trim($this->genres_helper, '{}');

		$array = (array)array_filter(explode(',', $str));

		foreach ($array as $key => $value) {
			if ($number == $value)
				unset($array[$key]);
		}

		$array = array_unique($array);

		$this->genres_helper = '{' . implode(',', $array) . '}';
	}

	public function refreshGenresHelper()
	{
		$ids = $this->genres()->pluck('id')->toArray();

		if (count($ids) > 0)
			$this->genres_helper = '{' . implode(',', $ids) . '}';
		else
			$this->genres_helper = null;
	}

	public function genres()
	{
		return $this->belongsToMany('App\Genre', 'book_genres', 'book_id', 'genre_id')
			->withPivot('order')
			->orderBy('order')
			->notMain();
	}

	public function scopeAny($query)
	{
		return $query->withoutGlobalScope(CheckedScope::class)->withoutGlobalScope(NotConnectedScope::class)->withTrashed();
	}

	public function scopeAnyNotTrashed($query)
	{
		return $query->withoutGlobalScope(CheckedScope::class)->withoutGlobalScope(NotConnectedScope::class);
	}

	public function scopeRememberCount($query, $minutes = 5, $refresh = false)
	{
		if ($refresh) Cache::forget('books_count');

		return Cache::remember('books_count', $minutes, function () use ($query) {
			return $query->count();
		});
	}

	public function scopeGenre($query, $genre_ids)
	{
		if (isset($genre_ids)) {
			foreach ($genre_ids as $k => $v)
				$genre_ids[$k] = intval($v);

			return $query->whereRaw('genres_helper && array[' . implode(',', $genre_ids) . ']');
			//return $query->leftJoin('book_genres', 'books.id', '=', 'book_id')->whereIn("genre_id", (array)$genre_ids);
		} else
			return $query;
	}

	public function scopeAndGenre($query, $genre_ids)
	{
		if (isset($genre_ids)) {
			foreach ($genre_ids as $k => $v)
				$genre_ids[$k] = intval($v);

			return $query->whereRaw('genres_helper && array[' . implode(',', $genre_ids) . ']');
			//return $query->leftJoin('book_genres', 'books.id', '=', 'book_id')->whereIn("genre_id", (array)$genre_ids);
		} else
			return $query;
	}

	public function scopeWithoutGenre($query, $genre_ids)
	{
		if (isset($genre_ids)) {

			foreach ($genre_ids as $k => $v)
				$genre_ids[$k] = intval($v);

			return $query->where(function ($query) use ($genre_ids) {
				$query->whereRaw('not genres_helper && array[' . implode(',', $genre_ids) . ']')
					->orWhereNull('genres_helper');
			});

		} else
			return $query;
	}

	public function scopeSimilaritySearch($query, $searchText)
	{
		$query->selectRaw("books.*, similarity(title, ?) AS rank", [$searchText]);

		$query->whereRaw("(" . $this->getTable() . ".title) % ?", [$searchText]);

		$query->orderBy("rank", 'desc');

		return $query;
	}

	public function scopeWordSimilaritySearch($query, $searchText)
	{
		$query->selectRaw("books.*, word_similarity(title, ?) AS rank", [$searchText]);

		$query->whereRaw("(title) %> ?", [$searchText]);

		$query->orderBy("rank", 'desc');

		return $query;
	}

	public function usersAddedToFavorites()
	{
		return $this->belongsToMany('App\User', 'user_books');
	}

	public function user_view_ips()
	{
		return $this->hasMany('App\BookViewIp');
	}

	public function view_count()
	{
		return $this->hasOne('App\ViewCount', 'book_id', 'id')
			->withDefault();
	}

	public function average_rating_for_period()
	{
		return $this->hasOne('App\BookAverageRatingForPeriod', 'book_id', 'id')
			->withDefault();
	}

	public function pages()
	{
		return $this->hasMany('App\Page');
	}

	public function cover()
	{
		return $this->belongsTo('App\Attachment', 'cover_id', 'id');
	}

	public function old_covers()
	{
		return $this->hasMany('App\BookCover', 'book_id', 'id');
	}

	function annotation()
	{
		return $this->hasOne('App\Section', 'book_id', 'id')
			->where('sections.type', '=', 'annotation');
	}

	function short_annotation()
	{
		return $this->hasOne('App\Section', 'book_id', 'id')
			->where('sections.type', '=', 'annotation')
			->with(['pages' => function ($query) {
				$query->where('page', '<', '2');
			}]);
	}

	public function files()
	{
		return $this->hasMany('App\BookFile', 'book_id', 'id');
	}

	public function votes()
	{
		return $this->hasMany('App\BookVote');
	}

	public function textProcessings()
	{
		return $this->hasMany('App\BookTextProcessing');
	}

	public function votesUsers()
	{
		return $this->belongsToMany('App\User', 'book_votes', 'book_id', 'create_user_id')
			->withPivot('vote', 'created_at')
			->wherePivot('deleted_at', null);
	}

	public function userVote()
	{
		return $this->hasOne('App\BookVote', 'book_id', 'id')->where("create_user_id", Auth::id());
	}

	public function deletedByUser()
	{
		return $this->belongsTo('App\User', 'delete_user_id', 'id');
	}

	public function keywords()
	{
		return $this->belongsToMany('App\Keyword', 'book_keywords', 'book_id', 'keyword_id')
			->orderBy('rating', 'desc')
			->withPivot('user_id', 'id', 'created_at', 'status_changed_at')
			//->using('App\BookKeyword')
			->whereNull('book_keywords.deleted_at')
			->addSelect("keywords.*")
			//->withUnchecked()
			// присоединяем голоса пользователя, если он вошел в аккаунт
			->when(Auth::check(), function ($query) {
				return $query->leftJoin('book_keyword_votes', function ($join) {
					$join->on('book_keywords.id', '=', 'book_keyword_votes.book_keyword_id')
						->on('book_keyword_votes.user_id', '=', DB::raw(Auth::id()));
				})
					->addSelect("book_keyword_votes.*");
			});
	}

	public function book_keywords()
	{
		return $this->hasMany('App\BookKeyword')
			->with("keyword")
			->when(Auth::check(), function ($query) {
				return $query->with("user_vote");
			});
	}

	public function userStatuses()
	{
		return $this->belongsToMany('App\User', 'book_statuses', 'book_id', 'user_id')
			->withPivot('user_updated_at');
	}

	public function users_read_statuses()
	{
		return $this->hasMany('App\BookStatus')
			->where('status', '!=', 'null');
	}

	public function statuses()
	{
		return $this->hasMany('App\BookStatus')
			->where('status', '!=', 'null');
	}

	public function origin_statuses()
	{
		return $this->hasMany('App\BookStatus')
			->where('status', '!=', 'null');
	}

	public function authors()
	{
		return $this->belongsToMany('App\Author', 'book_authors', 'book_id', 'author_id')
			->withPivot(['order', 'type'])
			->orderBy('order')
			->using(BookAuthor::class)
			->withTimestamps();
	}

	public function getAuthorsWithType($type)
	{
		return $this->authors->filter(function ($item, $key) use ($type) {
			return $item->pivot->type == $type;
		});
	}

	public function translators()
	{
		return $this->belongsToMany('App\Author', 'book_authors', 'book_id', 'author_id')
			->withPivot(['order', 'type'])
			->orderBy('order')
			->withPivotValue('type', AuthorEnum::Translator)
			->using(BookAuthor::class)
			->withTimestamps();
	}

	public function editors()
	{
		return $this->belongsToMany('App\Author', 'book_authors', 'book_id', 'author_id')
			->withPivot(['order', 'type'])
			->orderBy('order')
			->withPivotValue('type', AuthorEnum::Editor)
			->using(BookAuthor::class)
			->withTimestamps();
	}

	public function compilers()
	{
		return $this->belongsToMany('App\Author', 'book_authors', 'book_id', 'author_id')
			->withPivot(['order', 'type'])
			->orderBy('order')
			->withPivotValue('type', AuthorEnum::Compiler)
			->using(BookAuthor::class)
			->withTimestamps();
	}

	public function illustrators()
	{
		return $this->belongsToMany('App\Author', 'book_authors', 'book_id', 'author_id')
			->withPivot(['order', 'type'])
			->orderBy('order')
			->withPivotValue('type', AuthorEnum::Illustrator)
			->using(BookAuthor::class)
			->withTimestamps();
	}

	public function sequences()
	{
		return $this->belongsToMany('App\Sequence', 'book_sequences', 'book_id', 'sequence_id')
			->withPivot('number')
			->withPivot('order')
			->orderBy('order');
	}

	public function source()
	{
		return $this->hasOne('App\BookFile', 'book_id', 'id')
			->where('source', true);
	}

	public function language()
	{
		return $this->hasOne('App\Language', 'code', 'ti_lb');
	}

	public function originalLang()
	{
		return $this->hasOne('App\Language', 'code', 'ti_olb');
	}

	public function add_user()
	{
		return $this->hasOne('App\User', 'id', 'create_user_id');
	}

	public function edit_user()
	{
		return $this->hasOne('App\User', 'id', 'edit_user_id');
	}

	public function check_user()
	{
		return $this->hasOne('App\User', 'id', 'check_user_id');
	}

	public function scopeVoid($query)
	{
		return $query;
	}

	public function getNameForBookFile()
	{
		$name = '';

		foreach ($this->writers as $author) {
			$name .= trim($author->last_name . ' ' . $author->first_name . ' ' . $author->nickname);
			$name .= ' ';
		}

		$name .= trim(' ' . $this->title);
		/*
		$name = replaceAsc194toAsc32($name);
		$name = \transliterator_transliterate("Any-Latin; Latin-ASCII", $name);
		$name = preg_replace("/ʹ+/iu", "'", $name);
		$name = preg_replace("/([^[:alnum:]\№\ \'\~\`$\^\&\[\]\(\)])+/iu", "", $name);
		$name = preg_replace("/[[:space:]]+/iu", " ", $name);
		$name = mb_substr($name, 0, 150);
		$name = trim($name);
		$name = str_replace(" ", "_", $name);
*/
		if (!empty($this->redaction))
			$name .= ' r' . $this->redaction;

		$name = trim($name, '.');

		return trim($name);
	}

	public function scopeFulltextSearch($query, $searchText)
	{
		$searchText = replaceSimilarSymbols($searchText);

		$Ar = preg_split("/[\s,[:punct:]]+/", $searchText, 0, PREG_SPLIT_NO_EMPTY);

		$s = '';

		if ($Ar) {
			$s = "to_tsvector('english', \"title\") ";
			$s .= " @@ to_tsquery(quote_literal(quote_literal(?)) || ':*')";

			return $query->whereRaw($s, implode('+', $Ar));
		}

		return $query;
	}

	public function setRateInfoAttribute($array)
	{
		foreach (config('litlife.votes') as $vote) {
			if (isset($array[$vote])) {
				if (is_array($array[$vote]))
					$array[$vote] = $array[$vote]['count'];
				else
					$array[$vote] = intval($array[$vote]);
			}
		}

		$this->attributes['rate_info'] = $array ? serialize($array) : null;
	}

	public function getRateInfoAttribute($value)
	{
		$rate_info = unserialize($value);

		$max = @max($rate_info) ?? 0;

		$array = [];

		foreach (config('litlife.votes') as $vote) {

			if (!empty($max) and isset($rate_info[$vote]) and is_numeric($rate_info[$vote]))
				$array[$vote] = [
					'percent' => intval(round((100 * $rate_info[$vote]) / $max, 0)),
					'count' => $rate_info[$vote]
				];
			else
				$array[$vote] = [
					'percent' => 0,
					'count' => 0
				];
		}

		return $array;
	}

	function similars()
	{
		return $this->belongsToMany('App\Book', 'book_similar_votes', 'book_id', 'other_book_id')
			->selectRaw('"books".*, "other_book_id", SUM ("vote") as "sum"')
			->groupBy('books.id', "other_book_id", 'book_similar_votes.book_id')
			->with("cover", "authors");
	}

	public function similar_vote()
	{
		return $this->hasMany('App\BookSimilarVote', 'other_book_id', 'id');
	}

	public function ageStatus()
	{
		if ($this->age) {
			if (!auth()->check())
				return 'not_authorized';
			elseif (is_null(auth()->user()->born_date))
				return 'birthday_not_set';
			elseif (auth()->user()->born_date->addYears($this->age)->isFuture())
				return 'age_too_small';
			else
				return 'enable';
		}

		return 'enable';
	}

	public function parse()
	{
		return $this->hasOne('App\BookParse')
			->orderBy('created_at', 'desc')
			->orderBy('id', 'desc')
			->withDefault();
	}

	public function parses()
	{
		return $this->hasMany('App\BookParse');
	}

	public function isParsed()
	{
		return $this->parse->isSucceed();
	}

	/*
	 * Распарсена ли книга
	 */

	public function isFailedParse()
	{
		return $this->parse->isFailed();
	}

	/*
	 * Парсинг произошел с ошибкой
	 */

	public function isDescriptionOnly()
	{
		if ($this->files_count < 1 and $this->sections_count < 1 and $this->page_count < 1)
			return true;
		else
			return false;
	}

	/*
	 * У книги нет ни файлов для скачивания, ни глав для чтения
	 */

	public function isParseExist()
	{
		if (!empty($this->parse))
			return true;
		else
			return false;
	}

	/*
	 * Книга добавлена как обложка и не нуждается в парсинге
	 */

	/**
	 * Есть ли у книги любые текстовые страницы для чтения в старом или новом стиле
	 * @return bool
	 */

	public function isHavePagesToRead()
	{
		if ($this->isPagesNewFormat()) {
			if ($this->sections_count < 1 and $this->notes_count < 1)
				return false;
		} else {
			if ($this->page_count < 1)
				return false;
		}
		return true;
	}

	public function isPagesNewFormat()
	{
		return $this->online_read_new_format;
	}

	public function scopeOnlineReadNewFormat($query)
	{
		return $query->where('online_read_new_format', true);
	}

	public function setReadyStatusAttribute($text)
	{
		$this->attributes['ready_status'] = BookComplete::getValue($text);
	}

	public function getReadyStatusAttribute($value)
	{
		return BookComplete::getKey($value);
	}

	public function scopeWhereReadyStatus($query, $text)
	{
		foreach (BookComplete::toArray() as $item => $number) {
			if ($text == $item)
				return $query->where($this->getTable() . '.ready_status', $number);
		}
	}

	public function setSwearAttribute($text)
	{
		foreach ($this->swearArray as $number => $item) {
			if ($text == $item)
				$this->attributes['swear'] = $number;
		}

		if (empty($this->attributes['swear']))
			$this->attributes['swear'] = 0;
	}

	public function getSwearAttribute($value)
	{
		foreach ($this->swearArray as $number => $item) {
			if ($value == $number)
				return $item;
		}
		return $this->swearArray[0];
	}

	public function scopeWhereSwear($query, $text)
	{
		foreach ($this->completeStatusArray as $number => $item) {
			if ($text == $item)
				return $query->where($this->getTable() . '.swear', $number);
		}
	}

	public function scopeForTable($query)
	{
		return $query->with(['writers', 'sequences', 'genres', 'language'])
			->with(['statuses' => function ($query) {
				$query->where('user_id', auth()->id());
			}])
			->notConnected()
			->orderBy($this->getTable() . '.title', 'asc');
	}

	public function setTitleAttribute($title)
	{
		$title = trim($title);

		$title = replaceAsc194toAsc32($title);
		$title = preg_replace('/([[:space:]]+)/iu', ' ', $title);
		$title = preg_replace('/[\x00-\x1F\x7F]/u', '', $title);

		if (preg_match('/\[(С|C)И\]/iu', $title)) {
			$title = trim(preg_replace('/\[(С|C)И\]/iu', '', $title));
			$this->attributes['is_si'] = true;
		}

		if (preg_match('/\((С|C)И\)/iu', $title)) {
			$title = trim(preg_replace('/\((С|C)И\)/iu', '', $title));
			$this->attributes['is_si'] = true;
		}

		if (preg_match('/\(ЛП\)/iu', $title)) {
			$title = trim(preg_replace('/\(ЛП\)/iu', '', $title));
			$this->attributes['is_lp'] = true;
		}

		if (preg_match('/\[ЛП\]/iu', $title)) {
			$title = trim(preg_replace('/\[ЛП\]/iu', '', $title));
			$this->attributes['is_lp'] = true;
		}

		if (preg_match('/\[сборник\]/iu', $title)) {
			$title = trim(preg_replace('/\[сборник\]/iu', '', $title));
			$this->attributes['is_collection'] = true;
		}

		if (preg_match('/\(сборник\)/iu', $title)) {
			$title = trim(preg_replace('/\(сборник\)/iu', '', $title));
			$this->attributes['is_collection'] = true;
		}
		/*
				if (preg_match('/\.$/iu', $title) and !preg_match('/\.\.\.$/iu', $title) and !preg_match('/\.\.$/iu', $title)) {
					$title = trim(preg_replace('/(\.+)$/iu', '', $title));
				}
				*/
		if (mb_strlen($title) > 255)
			$title = mb_substr($title, 0, 255);

		$this->attributes['title'] = $title;
	}

	public function updateTitleAuthorsHelper()
	{
		$s = trim($this->attributes['title']);

		$s = replaceSimilarSymbols($s);

		$this->attributes['title_search_helper'] = mb_strtolower($s);
	}

	public function scopeWherePublishYearRange($query, $from = null, $till = null)
	{

		if (!empty($from = intval($from))) {
			$from = pg_smallintval($from);

			$query->where($this->getTable() . '.pi_year', '>=', $from);
		}

		if (!empty($till = intval($till))) {
			$till = pg_smallintval($till);

			$query->where($this->getTable() . '.pi_year', '<=', $till);
		}

		return $query;
	}

	public function scopeWhereWriteYearRange($query, $from = null, $till = null)
	{
		if (!empty($from = intval($from))) {
			$from = pg_smallintval($from);

			$query->where($this->getTable() . '.year_writing', '>=', $from);
		}

		if (!empty($till = intval($till))) {
			$till = pg_smallintval($till);

			$query->where($this->getTable() . '.year_writing', '<=', $till);
		}

		return $query;
	}

	public function scopeWherePagesCountRange($query, $min = null, $max = null)
	{
		if (!empty($min = intval($min)))
			$query->where($this->getTable() . '.page_count', '>=', pg_intval($min));

		if (!empty($max = intval($max)))
			$query->where($this->getTable() . '.page_count', '<=', pg_intval($max));

		return $query;
	}

	public function isWaitedCreateNewBookFiles()
	{
		return (bool)$this->need_create_new_files;
	}

	public function changed()
	{
		if (!$this->isPagesNewFormat())
			return false;

		if ($this->sections_count < 1)
			return false;

		$this->redaction = $this->redaction + 1;
		//$this->user_edited_at = now();
		$this->needCreateNewBookFiles();

		return true;
	}

	public function needCreateNewBookFiles()
	{
		$this->need_create_new_files = true;
	}

	public function needCreateNewBookFilesDisable()
	{
		$this->need_create_new_files = false;
	}

	public function scopeWaitedNeedCreateNewBookFiles($query)
	{
		return $query->where('need_create_new_files', true);
	}

	public function scopeWhereNeedCreateNewBookFilesCooldownIsOver($query)
	{
		return $query->where(function ($query) {
			$time = now()->subMinutes(config('litlife.cooldown_for_create_new_book_files_after_edit'));

			$query->where('user_edited_at', '<', $time)
				->orWhereNull('user_edited_at');
		});
	}

	public function minutesTillNewFilesWillBeCreated()
	{
		$time = now()->subMinutes(config('litlife.cooldown_for_create_new_book_files_after_edit'));

		if ($this->user_edited_at > $time)
			return $this->user_edited_at->diffInMinutes($time);
		else
			return 0;
	}

	public function scopeOldestUserUpdated($query)
	{
		return $query->orderBy('user_edited_at', 'desc')
			->orderBy('id', 'desc');
	}

	public function scopeLatestUserUpdated($query)
	{
		return $query->orderBy('user_edited_at', 'asc')
			->orderBy('id', 'asc');
	}

	public function isRatingChanged()
	{
		return (bool)$this->refresh_rating;
	}

	public function ratingChanged()
	{
		$this->refresh_rating = true;
	}

	public function updateAwardsCount()
	{
		$this->awards_count = $this->awards()->count();
		$this->save();
	}

	public function awards()
	{
		return $this->hasMany('App\BookAward')->with('award');
	}

	public function getShareTitle()
	{
		return __('book.book') . ' "' . $this->title . '" - ' . implode(', ', $this->writers->pluck('name_helper')->toArray());
	}

	public function getShareDescription()
	{
		return mb_substr(strip_tags(optional($this->annotation)->getContent()), 0, 200);
	}

	public function getShareImage()
	{
		if (!empty($this->cover))
			return $this->cover->fullUrlMaxSize(500, 500);
		else
			return '';
	}

	public function getManagerAssociatedWithUser($user)
	{
		if ($this->authors->count() < 1)
			return false;

		$this->loadMissing('authors.managers');

		foreach ($this->authors as $author) {
			foreach ($author->managers as $manager) {
				if ($manager->user_id == $user->id) {
					if ($manager->isAccepted())
						return $manager;
				}
			}
		}

		return false;
	}

	public function getAuthorsManagerAssociatedWithUser(User $user): \Illuminate\Support\Collection
	{
		$authors = new \Illuminate\Support\Collection();

		if ($this->authors->count() < 1)
			return $authors;

		foreach ($this->authors as $author) {
			foreach ($author->managers as $manager) {
				if ($manager->user_id == $user->id) {
					if ($manager->isAccepted() and $manager->isAuthorCharacter()) {
						$authors->push($author);
					}
				}
			}
		}

		return $authors;
	}

	public function getSEODescription()
	{
		$description = ' ';

		if ($this->isReadAccess()) {
			if ($this->page_count > 0) {
				$description .= trans_choice('page.read_pages_count', $this->page_count, ['count' => $this->page_count]) . ' 📚 ';
			}
		}

		if ($this->isDownloadAccess() and $this->files->count() > 0) {
			$description .= mb_ucfirst(__('common.download')) . ' ' .
				implode(' ', $this->files->pluck('format')->transform(function ($item, $key) {
					return mb_strtoupper($item);
				})->toArray()) . '. ';
		}

		if (!empty($this->short_annotation)) {
			$description .= mb_substr(trim(strip_tags($this->short_annotation->getContent())), 0, 300);
		}

		return trim($description);
	}

	public function seller()
	{
		if ($this->authors->count() < 1)
			return false;

		foreach ($this->getAuthorsWithType('writers') as $author) {
			foreach ($author->managers as $manager) {
				if ($manager->isAccepted() and $manager->can_sale)
					return $manager->user;
			}
		}

		return false;
	}

	public function seller_manager()
	{
		if ($this->authors->count() < 1)
			return false;

		foreach ($this->getAuthorsWithType('writers') as $author) {
			foreach ($author->managers as $manager) {
				if ($manager->isAccepted() and $manager->can_sale)
					return $manager;
			}
		}

		return false;
	}

	public function getDiffBeetweenLastPriceChangeInDays()
	{
		if (empty($this->price_updated_at))
			return 0;

		if ($this->price_updated_at->addDays(config('litlife.book_price_update_cooldown'))->isPast())
			return 0;

		return (integer)ceil($this->price_updated_at->addDays(config('litlife.book_price_update_cooldown'))->floatDiffInDays(now()));
	}

	public function getSellTitle()
	{
		return ' "' . $this->title . '" - ' . implode(', ', $this->writers->pluck('name_helper')->toArray());
	}

	public function refreshCharactersCount()
	{
		dispatch(new BookUpdateCharactersCountJob($this));
	}

	public function getCharactersCountAttribute($value)
	{
		return intval($value);
	}

	public function setPriceAttribute($value)
	{
		if (empty($value))
			$this->attributes['price'] = null;
		else
			$this->attributes['price'] = number_format($value, 2, '.', '');
	}

	public function getPriceAttribute($value)
	{
		$integer_value = (integer)$value;

		if ($value == $integer_value)
			return $integer_value;
		else
			return (float)$value;
	}

	public function setPreviousPriceAttribute($value)
	{
		if (empty($value))
			$this->attributes['previous_price'] = null;
		else
			$this->attributes['previous_price'] = number_format($value, 2, '.', '');
	}

	public function getPreviousPriceAttribute($value)
	{
		$integer_value = (integer)$value;

		if ($value == $integer_value)
			return $integer_value;
		else
			return (float)$value;
	}

	public function character_change_history()
	{
		return $this->hasMany('App\BookCharacterChange');
	}

	public function rememberPageForUser($user, int $page, int $section_id = null)
	{
		DB::transaction(function () use ($user, $page, $section_id) {
			ignoreDuplicateException(function () use ($user, $page, $section_id) {
				$remembered_page = $this->remembered_pages()
					->where('user_id', $user->id)
					->first();

				if (empty($remembered_page)) {
					$remembered_page = new BookReadRememberPage;
					$remembered_page->user_id = $user->id;
				}

				$remembered_page->page = $page;
				$remembered_page->inner_section_id = $section_id;
				$remembered_page->characters_count = $this->characters_count;
				$this->remembered_pages()->save($remembered_page);

				if ($remembered_page->isChanged('characters_count'))
					$user->flushCachedFavoriteBooksWithUpdatesCount();
			});
		});
	}

	public function remembered_pages()
	{
		return $this->hasMany('App\BookReadRememberPage');
	}

	public function getRememberedPageCharacterCountDifference()
	{
		$remembered_page = $this->remembered_pages->first();

		$count = 0;

		if (!empty($remembered_page)) {
			if ($remembered_page->characters_count < $this->characters_count) {
				$count = $this->characters_count - $remembered_page->characters_count;
			}
		}

		return $count;
	}

	public function sqlite()
	{
		if (empty($this->sqlite)) {
			$path = xsBookPath::GetPathToSqliteDB($this->id);

			$this->sqlite = new BookSqlite($this);
			$this->sqlite->connect($path);
		}

		return $this->sqlite;
	}

	public function boughtUsers()
	{
		return $this->belongsToMany('App\User', 'user_purchases', 'purchasable_id', 'buyer_user_id')
			->where('purchasable_type', '=', 'book')
			->withPivot(['created_at'])
			->wherePivot('canceled_at', null);
	}

	public function boughtTimesCountRefresh()
	{
		$this->bought_times_count = $this->purchases()->count();
		$this->save();
	}

	public function purchases()
	{
		return $this->morphMany('App\UserPurchase', 'purchasable')
			->notCanceled();
	}

	public function getBookPath()
	{
		return xsBookPath::GetPathToSqliteDB($this->id);
	}

	public function refreshFavoritesCount()
	{
		$this->added_to_favorites_count = $this->library_users()->count();
	}

	public function library_users()
	{
		return $this->hasMany('App\UserBook');
	}

	public function setCommentsClosedAttribute($value)
	{
		$this->attributes['comments_closed'] = intval(boolval($value));
	}

	public function getCommentsClosedAttribute($value)
	{
		return boolval($value);
	}

	public function forceDeleteImagesExceptCover()
	{
		if (!empty($this->cover))
			$this->attachments()
				->where('id', '!=', $this->cover->id)
				->forceDelete();
		else
			$this->attachments()
				->forceDelete();
	}

	public function attachments()
	{
		return $this->hasMany('App\Attachment', 'book_id', 'id');
	}

	public function forceDeleteImages()
	{
		$this->attachments()->forceDelete();
	}

	public function forceDeleteSectionNote()
	{
		DB::transaction(function () {

			$sections = $this->sections()
				->whereIn('type', ['section', 'note'])
				->get();

			foreach ($sections as $section) {
				$section->forceDelete();
			}
		});
	}

	public function sections()
	{
		return $this->hasMany('App\Section');
	}

	public function refreshSectionsCount()
	{
		UpdateBookSectionsCount::dispatch($this);
	}

	public function refreshNotesCount()
	{
		UpdateBookNotesCount::dispatch($this);
	}

	public function refreshAttachmentCount()
	{
		UpdateBookAttachmentsCount::dispatch($this);
	}

	public function refreshFilesCount()
	{
		UpdateBookFilesCount::dispatch($this);
	}

	public function refreshPagesCount()
	{
		UpdateBookPagesCount::dispatch($this);
	}

	public function refreshBookAgeCount()
	{
		UpdateBookAge::dispatch($this);
	}

	public function scopeOrderByRatingDesc($query)
	{
		return $query->orderBy('in_rating', 'desc')
			->orderBy('vote_average', 'desc')
			->orderBy('user_vote_count', 'desc')
			->orderBy('books.id', 'desc');
	}

	public function scopeOrderByRatingAsc($query)
	{
		return $query->orderBy('in_rating', 'desc')
			->orderBy('vote_average', 'asc')
			->orderBy('user_vote_count', 'desc')
			->orderBy('books.id', 'desc');
	}

	public function scopeWhereISBN($query, $value)
	{
		return $query->where('pi_isbn', 'ILIKE', '%' . preg_quote($value) . '%');
	}

	public function scopeTitleAuthorsFulltextSearch($query, $searchText)
	{
		$searchText = trim($searchText);

		if (preg_match('/^\"([[:print:]]+)\"$/iu', $searchText, $matches)) {
			$query->where('title', 'ILIKE', $matches[1]);
		} else {
			$searchText = replaceSimilarSymbols($searchText);

			$array = preg_split("/[\s,[:punct:]]+/", $searchText, 0, PREG_SPLIT_NO_EMPTY);

			if ($array) {
				$s = "to_tsvector('english', \"title_search_helper\") ";
				$s .= " @@ to_tsquery(quote_literal(quote_literal(?)) || ':*')";

				return $query->whereRaw($s, implode('+', $array));
			}
		}

		return $query;
	}

	public function scopePublishCityILike($query, $value)
	{
		$value = preg_quote($value);

		return $query->where('pi_city', 'ILIKE', $value);
	}

	public function setForbidToChangeAttribute($value)
	{
		if ($value)
			$this->attributes['forbid_to_change'] = true;
		else
			$this->attributes['forbid_to_change'] = null;
	}

	public function getForbidToChangeAttribute($value)
	{
		return (boolean)$value;
	}

	public function isCanChange($user = null)
	{
		if ($this->forbid_to_change) {
			if (empty($user))
				return false;

			if (!$user->getPermission('enable_disable_changes_in_book'))
				return false;
		}

		return true;
	}

	public function setPrivateChaptersCountAttribute($value)
	{
		if (empty($value))
			$value = null;

		$this->attributes['private_chapters_count'] = $value;
	}

	public function getPrivateChaptersCountAttribute($value)
	{
		return intval($value);
	}

	public function refreshPrivateChaptersCount()
	{
		$this->private_chapters_count = intval($this->sections()
			->private()
			->chapter()
			->count());
	}

	public function isDisplaySaleWarning()
	{
		if ($this->isForSale() and !$this->isReadOrDownloadAccess())
			return true;

		if ($this->free_sections_count >= $this->sections_count)
			return true;

		if ($this->isForSale() and $this->isPrivate())
			return true;

		return false;
	}

	public function isForSale()
	{
		return (bool)$this->price;
	}

	public function isPostedFreeFragment()
	{
		if (!$this->isForSale())
			return false;

		if ($this->free_sections_count > 0)
			return true;
		else
			return false;
	}

	public function scopePaid($query)
	{
		return $query->whereNotNull('price');
	}

	public function scopeFree($query)
	{
		return $query->whereNull('price');
	}

	public function setMaleVotePercentAttribute($value)
	{
		if (empty($value))
			$value = null;

		$this->attributes['male_vote_percent'] = $value;
	}

	public function refreshCommentsCount($updateOther = true)
	{
		if ($this->isInGroup()) {
			if ($this->isNotMainInGroup())
				$mainBook = $this->mainBook;
			else
				$mainBook = $this;

			if (!empty($mainBook)) {
				$mainBook->comment_count = $mainBook->comments()->accepted()->count();
				$mainBook->save();

				$mainBook->groupedBooks()
					->update([
						'comment_count' => $mainBook->comment_count
					]);
			}
		} else {
			$this->comment_count = $this->comments()->accepted()->count();
			$this->save();
		}
	}

	function comments()
	{
		return $this->morphMany('App\Comment', 'commentable');
	}

	function commentsOrigin()
	{
		return $this->morphMany('App\Comment', 'commentable', 'commentable_type', 'origin_commentable_id');
	}

	public function priceChangeLogs()
	{
		return $this->hasMany('App\PriceChangeLog');
	}

	public function getDiscount()
	{
		if ($this->isPriceHasBecomeLess())
			return floor(($this->previous_price - $this->price) / ($this->previous_price / 100));
		else
			return false;
	}

	public function isPriceHasBecomeLess()
	{
		return (boolean)($this->price < $this->previous_price);
	}

	public function scopeOrderByRatingDayDesc($query)
	{
		return $query->addSelect('books_average_rating_for_period.day_rating')
			->join('books_average_rating_for_period', 'books.id', '=', 'books_average_rating_for_period.book_id')
			->orderBy('books_average_rating_for_period.day_rating', 'desc')
			->orderBy('books_average_rating_for_period.all_rating', 'desc');
	}

	public function scopeOrderByRatingWeekDesc($query)
	{
		return $query->addSelect('books_average_rating_for_period.week_rating',
			'books_average_rating_for_period.week_votes_count',
			'books_average_rating_for_period.week_vote_average')
			->join('books_average_rating_for_period', 'books.id', '=', 'books_average_rating_for_period.book_id')
			->orderBy('books_average_rating_for_period.week_rating', 'desc')
			->orderBy('books_average_rating_for_period.all_rating', 'desc');
	}

	public function scopeOrderByRatingMonthDesc($query)
	{
		return $query->addSelect('books_average_rating_for_period.month_rating',
			'books_average_rating_for_period.month_votes_count',
			'books_average_rating_for_period.month_vote_average')
			->join('books_average_rating_for_period', 'books.id', '=', 'books_average_rating_for_period.book_id')
			->orderBy('books_average_rating_for_period.month_rating', 'desc')
			->orderBy('books_average_rating_for_period.all_rating', 'desc');
	}

	public function scopeOrderByRatingQuarterDesc($query)
	{
		return $query->addSelect('books_average_rating_for_period.quarter_rating',
			'books_average_rating_for_period.quarter_votes_count',
			'books_average_rating_for_period.quarter_vote_average')
			->join('books_average_rating_for_period', 'books.id', '=', 'books_average_rating_for_period.book_id')
			->orderBy('books_average_rating_for_period.quarter_rating', 'desc')
			->orderBy('books_average_rating_for_period.all_rating', 'desc');
	}

	public function scopeOrderByRatingYearDesc($query)
	{
		return $query->addSelect('books_average_rating_for_period.year_rating',
			'books_average_rating_for_period.year_votes_count',
			'books_average_rating_for_period.year_vote_average')
			->join('books_average_rating_for_period', 'books.id', '=', 'books_average_rating_for_period.book_id')
			->orderBy('books_average_rating_for_period.year_rating', 'desc')
			->orderBy('books_average_rating_for_period.all_rating', 'desc');
	}

	public function setPiYearAttribute($value)
	{
		if (empty($value))
			$value = null;

		$this->attributes['pi_year'] = $value;
	}

	public $free_fragment_characters_count = null;

	public function getFreeFragmentCharactersPercentage(): int
	{
		$freeFragmentCharactersCount = $this->getFreeFragmentCharactersCount();

		if ($this->characters_count > 0) {

			$percent = round((100 * $freeFragmentCharactersCount / $this->characters_count), 0);

			if ($percent > 100)
				$percent = 100;

			if ($percent < 0)
				$percent = 0;

			return $percent;
		}

		return 0;
	}

	public function getFreeFragmentCharactersCount(): int
	{
		if (is_null($this->free_fragment_characters_count)) {

			if ($this->free_sections_count < 1)
				return 0;

			$freeSections = $this->sections()
				->chapter()
				->accepted()
				->defaultOrder()
				->limit($this->free_sections_count)
				->get();

			$this->free_fragment_characters_count = $freeSections->sum('character_count');
		}

		return $this->free_fragment_characters_count;
	}

	public function publish()
	{
		$this->statusAccepted();

		// отмечаем любых непроверенных авторов, как проверенные
		foreach ($this->authors as $author) {
			if (!$author->isAccepted()) {
				$author->statusAccepted();
				$author->save();
			}
		}

		// отмечаем любых непроверенных авторов, как проверенные
		foreach ($this->sequences as $sequence) {
			if (!$sequence->isAccepted()) {
				$sequence->statusAccepted();
				$sequence->save();
			}
		}

		$book_keywords = $this->book_keywords()->unaccepted()->get();

		if ($book_keywords->count() > 0) {
			foreach ($book_keywords as $book_keyword) {
				$book_keyword->statusAccepted();
				$book_keyword->save();
			}

			BookKeyword::flushCachedOnModerationCount();
		}

		foreach ($this->files()->unaccepted()->get() as $file) {
			$file->statusAccepted();
			$file->save();
		}

		$this->genres()->get()->each(function ($genre) {
			UpdateGenreBooksCount::dispatch($genre);
		});

		$this->authors->each(function ($author) {
			$author->ratingChanged();
			UpdateAuthorBooksCount::dispatch($author);
			$author->flushUsersAddedToFavoritesNewBooksCount();
		});

		$this->sequences->each(function ($sequence) {
			UpdateSequenceBooksCount::dispatch($sequence);
		});
	}

	public function deletingOnlineReadAndFiles()
	{
		DB::transaction(function () {

			$this->files()->delete();

			$this->sections()->where('type', '!=', 'annotation')->delete();

			$cover = $this->cover;

			$this->attachments()->when($cover, function ($query) use ($cover) {
				$query->where('id', '!=', $cover->id);
			})->delete();

			$this->online_read_new_format = true;
			$this->readAccessDisable();
			$this->downloadAccessDisable();
			$this->refreshSectionsCount();
			$this->refreshNotesCount();
			$this->refreshAttachmentCount();
			$this->refreshFilesCount();
			$this->refreshPagesCount();
			$this->save();
		});
	}

	public function isUserVerifiedAuthorOfBook(User $user): bool
	{
		if (optional($this->getManagerAssociatedWithUser($user))->character == 'author')
			return true;
		else
			return false;
	}

	public function changePrice($price): bool
	{
		if ($price < 1)
			$price = 0;

		$this->previous_price = $this->price;
		$this->price = $price;
		$this->price_updated_at = now();
		$this->save();

		$this->priceChangeLogs()
			->save(new PriceChangeLog([
				'price' => $price
			]));

		return true;
	}

	public $first_paid_section = null;

	public function getFirstPaidSection(): Section
	{
		if (is_null($this->first_paid_section)) {
			$section = Section::scoped(['book_id' => $this->id, 'type' => 'section'])
				->defaultOrder()
				->limit($this->free_sections_count + 1)
				->get();

			$this->first_paid_section = $section->last();
		}

		return $this->first_paid_section;
	}

	public function isEditionDetailsFilled(): bool
	{
		if (!empty(trim($this->pi_pub)))
			return true;

		if (!empty(trim($this->pi_city)))
			return true;

		if (!empty(trim($this->pi_year)))
			return true;

		if (!empty(trim($this->pi_isbn)))
			return true;

		return false;
	}

	public function setCommentsCountAttribute($value)
	{
		$this->attributes['comment_count'] = intval($value);
	}

	public function getCommentsCountAttribute()
	{
		return intval($this->comment_count);
	}

	public function updatePageNumbers()
	{
		BookUpdatePageNumbersJob::dispatch($this);
	}

	public function getVoteAverageForTable()
	{
		$number = number_format($this->vote_average, 2, '.', '');

		if ($number == 10)
			return '10.0';

		if ($number == 0)
			return 0;

		return $number;
	}
}

