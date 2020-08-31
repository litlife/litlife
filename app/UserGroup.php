<?php

namespace App;

use App\Model as Model;
use App\Traits\Cachable;
use Eloquent;
use GeneaLabs\LaravelModelCaching\CachedBuilder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * App\UserGroup
 *
 * @property string $name
 * @property string|null $permissions
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
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
 * @method static CachedBuilder|UserGroup newModelQuery()
 * @method static CachedBuilder|UserGroup newQuery()
 * @method static Builder|UserGroup onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static CachedBuilder|UserGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserGroup show()
 * @method static Builder|Model void()
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
 * @method static Builder|UserGroup withTrashed()
 * @method static Builder|UserGroup withoutTrashed()
 * @mixin Eloquent
 */
class UserGroup extends Model
{
	use SoftDeletes;
	use Cachable;

	public $permissions =
		[
			'notify_assignment' => false,
			'not_show_ad' => false, // не показывать рекламу

			'change_users_group' => false, // изменять группы у пользователей
			'manage_users_groups' => false, // добавлять, редактировать, удалять группы пользователей
			'user_moderate' => false, // удалять и добавлять пользователей на модерирование сообщений
			'user_delete' => false, // удалять и восстанавливать аккаунты пользователей
			'user_suspend' => false, // отключать и включать аккаунты пользователей

			'add_comment' => false, // добавлять комментарии
			'comment_self_edit_only_time' => false, // редактировать свои комментарии только в течениe  секунд
			'comment_edit_my' => false, // редактировать свои комментарии
			'comment_edit_other_user' => false, // редактировать чужие коментарии
			'delete_my_comment' => false, // удалять свои комментарии
			'delete_other_user_comment' => false, // удалять комментарии других пользователей
			'comment_view_who_likes_or_dislikes' => false, // просматривать кому понравился или не понравился комментарий
			//'vote_for_comment' => false, // голосовать за комментарии
			'comment_add_vote' => false, // голосовать за комментарии
			'book_comments_manage' => false, // открывать и закрывать комментарии к книгам

			'add_book' => false, // добавлять книги
			'add_book_without_check' => false, // добавлять книги без проверки
			'edit_self_book' => false, // редактировать свои книги
			'edit_other_user_book' => false, // редактировать книги чужих людей
			'delete_self_book' => false, // удалять свои книги
			'delete_other_user_book' => false, // удалять книги чужих людей
			'check_books' => false, // проверять книги
			'connect_books' => false, // соединять книги
			'book_secret_hide_set' => false, // закрывать/открывать доступ к книгам'
			'vote_for_book' => false, // оценивать книги / _удалять оценки
			'book_rate_other_user_remove' => false, // удалять чужие оценки

			'author_edit' => false, // редактировать автора
			'delete_hide_author' => false, // удалять авторов
			//'manage_author' => false, // управлять авторами
			'merge_authors' => false, // соединять авторов
			'author_group_and_ungroup' => false, // группировать и разгруппировывать авторов

			'sequence_delete' => false, // удалять/восстанавливать серии
			'sequence_edit' => false, // редактировать серии
			'sequence_merge' => false, // объединять серии

			'send_message' => false, // отправлять или редактировать личные сообщения
			'delete_message' => false, // удалять личные сообщения

			//'delete_hide_self_book' => false, // удалять-скрывать, восстанавливать свои книги
			//'delete_hide_other_user_book' => false, // удалять-скрывать, восстанавливать книги чужих людей

			'author_repeat_report_add' => false, // сообщять о том, что автор поворяется
			'author_repeat_report_delete' => false, // удалять любые записи, что автор поворяется
			'author_repeat_report_edit' => false, // редактировать свои записи, что автор поворяется

			'forum_group_handle' => false, // добавлять, удалять, редактировать, перемещать группы форумов

			'add_forum_forum' => false, // добавлять форумы
			'forum_edit_forum' => false, // редактировать форумы
			'delete_forum_forum' => false, // удалять форумы
			'forum_list_manipulate' => false, // управлять списками форумов

			'add_forum_topic' => false, // добавлять темы
			'delete_forum_self_topic' => false, // удалять свои темы
			'delete_forum_other_user_topic' => false, // удалять темы чужих людей
			'edit_forum_self_topic' => false, // редактировать свои темы
			'edit_forum_other_user_topic' => false, // редактировать темы чужих людей
			'manipulate_topic' => false, // управлять темами

			'add_forum_post' => false, // добавлять сообщения

			'forum_edit_self_post_only_time' => false, // редактировать свои сообщения только в течениe  секунд
			'forum_edit_self_post' => false, // редактировать свои сообщения всегда
			'forum_edit_other_user_post' => false, // редактировать сообщения других пользователей
			'forum_delete_self_post' => false, // удалять свои сообщения
			'forum_delete_other_user_post' => false, // удалять сообщения чужих людей
			'forum_topic_merge' => false, // объединять темы
			//'forum_move_topic' => false, // перемещать темы
			'forum_move_post' => false, // перемещать посты + _показать кнопку _удалить отмеченные посты
			'forum_post_manage' => false, // управлять постами (закреплять и откреплять посты)

			'blog' => false, // включить блог
			'blog_other_user' => false, // управлять чужим блогом

			'moderator_add_remove' => false, // добавлять/удалять модераторов
			'author_editor_request' => false, // отправлять запросы "стать автором или редактором"
			'author_editor_check' => false, // проверять запросы "стать автором или редактором"

			//'book_manage' => false, // управлять книгами автора

			'book_file_add' => false, // добавлять файлы ко всем публичным книгам
			'book_file_add_without_check' => false, // без проверки
			'book_file_add_to_self_book_without_check' => false, // без проверки, только к своим книгам
			'book_file_add_check' => false, // проверять добавленные файлы книг
			'book_file_delete' => false, // удалять любые файлы книг
			'book_file_edit' => false, // редактировать описания файлов книг

			'book_keyword_add' => false, // Прикреплять существующие ключевые слова к книгам без проверки
			'book_keyword_add_new_with_check' => false, // Добавлять новые ключевые слова с проверкой
			'book_keyword_moderate' => false, // Проверять ключевые слова и добавлять новые без проверки
			'book_keyword_remove' => false, // удалять ключевые слова
			'book_keyword_edit' => false, // редактировать ключевые слова
			'book_keyword_vote' => false, // ставить оценки +/- ключевым словам

			'book_fb2_file_convert_divide_on_page' => false, // преобразовать fb2, epub файл в страницы онлайн чтения

			'book_similar_vote' => false, // добавлять или голосовать похожие книги

			'genre_add' => false, // добавлять жанры
			'like_click' => false, // лайкать

			'edit_profile' => false, // редактировать свой профиль
			'edit_other_profile' => false, // редактировать чужие профили

			'add_genre_to_blacklist' => false, // добавлять жанр в черный список

			'text_block' => false, // добавлять или редактировать текстовый блок
			'admin_comment' => false, // кому видны и кому можно редактировать заметки для администрации

			'complain' => false, // отправять жалобы
			'complain_check' => false, // обрабатывать жалобы
			'check_post_comments' => false, // проверять комментарии и сообщения на модерации
			'access_to_closed_books' => false, // досуп к закрытым книгам
			'admin_panel_access' => false, // досуп к панели настроек и различных функций сайта
			'retry_failed_book_parse' => false, // отправить книгу на повторную обработку, если ее добавление произошло с ошибкой
			'achievement' => false,  // 'добавление, удаление, присваивание достижений
			'watch_activity_logs' => false,
			//'use_red_color' => false, //
			'display_technical_information' => false,
			'refresh_counters' => false,
			'awards' => false, // добавление, редактирование или удаление книжных наград
			'access_send_private_messages_avoid_privacy_and_blacklists' => false, // Доступ к отправке личных сообщений при ограничении приватности и в обход ЧС пользователя
			'shop_enable' => false,
			'author_sale_request_review' => false,
			'withdrawal' => false,
			'transfer_money' => false,
			'view_financial_statistics' => false,
			'manage_collections' => false,
			'see_deleted' => false,
			'edit_field_of_public_domain' => false,
			'enable_disable_changes_in_book' => false,
			'edit_other_user_collections' => false,
			'manage_mailings' => false,
			'create_text_processing_books' => false,
			'view_user_surveys' => false,
			'deleting_online_read_and_files' => false
		];
	protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);

		$this->attributes = array_merge($this->attributes, $this->permissions);
	}

	public function users()
	{
		return $this->hasMany('App\User', 'user_group_id');
	}

	public function getRulesAttribute()
	{
		$rules = $this->attributes;

		foreach ($this->attributes as $key => $value)
			$rules[mb_ucfirst(Str::camel($key))] = $value;

		return $rules;
	}

	public function getAsSnakeCase($keyName)
	{
		return $this->attributes[Str::snake($keyName)];
	}

	public function getPermissions()
	{
		return array_keys($this->permissions);
	}

	public function scopeShow($query)
	{
		return $query->where('show', true);
	}

	public function scopeWhereName($query, $name)
	{
		return $query->where('name', 'ilike', $name);
	}

	/*
		public function setPermissionsAttribute($permissions)
		{

			$new_array = [];

			foreach ($this->permissions as $permission) {
				$name = $permission[0];

				if (isset($permissions[$name]))
					$new_array[$name] = $permissions[$name];
				else
					$new_array[$name] = '0';
			}


			$this->attributes['permissions'] = serialize($new_array);

		}
			*/
}
