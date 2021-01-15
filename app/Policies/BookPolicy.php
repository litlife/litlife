<?php

namespace App\Policies;

use App\Book;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;

class BookPolicy extends Policy
{
    /**
     * Проверка может ли пользователь добавлять книги
     *
     * @param  User  $auth_user
     */
    public function create(User $auth_user)
    {
        return (boolean) $auth_user->getPermission('add_book');
    }

    public function view(User $auth_user, Book &$book)
    {
        if ($book->isPrivate()) {
            if ($book->isUserCreator($auth_user)) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function update(User $auth_user, Book &$book)
    {
        // книга уже распарсена или не нуждается в распарсивании
        if (!$book->parse->isSucceed()) // книга еще не обработана или не нуждается в обработке
        {
            return $this->deny(__('book.the_book_has_not_been_processed_yet'));
        }

        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        // книга в личном облаке
        if ($book->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($book->isUserCreator($auth_user)) {
                return true;
            } else {
                if ($auth_user->getPermission('check_books')) {
                    return true;
                }
            }
        } else {

            $character = optional($book->getManagerAssociatedWithUser($auth_user))->character;

            if ($character == 'author') {
                return true;
            }

            if ($character == 'editor') {
                if ($book->isUserCreator($auth_user) and !$book->isEditionDetailsFilled()) {
                    return true;
                }
            }

            if ($book->isUserCreator($auth_user)) {
                return (boolean) $auth_user->getPermission('edit_self_book');
            } else {
                return (boolean) $auth_user->getPermission('edit_other_user_book');
            }
        }

        return $this->deny(__('book.you_do_not_have_the_right_to_edit_the_description_of_this_book'));
    }

    /**
     * Может ли пользователь опубликовать книгу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function publish(User $auth_user, Book $book)
    {
        if (!$book->parse->isSucceed() and !$book->isDescriptionOnly()) // книга еще не обработана или не нуждается в обработке
        {
            return false;
        }

        if ($book->isAccepted()) // книга опубликована
        {
            return false;
        } elseif ($book->isSentForReview()) {
            // если книга добавлена пользователем
            if ($book->isUserCreator($auth_user)) {
                // и у пользователя есть право публиковать книгу
                if ($auth_user->getPermission('add_book_without_check')) {
                    return true;
                }
            } else {
                if ($auth_user->getPermission('check_books')) {
                    return true;
                }
            }
        } elseif ($book->isPrivate()) {

            // если книга добавлена пользователем
            if ($book->isUserCreator($auth_user)) {
                return true;
            } else {
                if ($auth_user->getPermission('check_books')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Может ли пользователь принять книгу в общую библиотеку
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function makeAccepted(User $auth_user, Book $book)
    {
        if (!$book->parse->isSucceed() and !$book->isDescriptionOnly()) // книга еще не обработана или не нуждается в обработке
        {
            return false;
        }

        if ($book->isAccepted()) // книга принята в общую библиотеку
        {
            return false;
        }

        // если книга добавлена пользователем
        if ($book->isUserCreator($auth_user)) {
            // и у пользователя есть право добавлять книгу в общую библиотеку без опубликования
            if ($auth_user->getPermission('add_book_without_check')) {
                return true;
            }
        } else {
            if ($auth_user->getPermission('check_books')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Может ли пользователь отправить книгу снова только в приватный доступ только для пользователя
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function addToPrivate(User $auth_user, Book $book)
    {
        if ($book->trashed()) {
            return false;
        }

        if ($book->isPrivate()) {
            return false;
        }

        if ($book->bought_times_count > 0) {
            return false;
        }

        if ($auth_user->getPermission('CheckBooks')) {
            return true;
        }

        return false;
    }

    /**
     * Может ли пользователь удалить книгу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function delete(User $auth_user, Book $book)
    {
        if ($book->trashed()) // книга уже удалена, поэтому запрещаем
        {
            return false;
        }

        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if ($book->bought_times_count > 0) {
            if ($book->isRejected()) {
                if ($book->status_changed_at->addDays(config('litlife.book_removed_from_sale_cooldown_in_days'))->isPast()) {
                    return $auth_user->can('author', $book);
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        if ($book->isPrivate()) {
            // если книга из личного облака и если книга добавлена пользователем, то разрешаем
            if ($book->isUserCreator($auth_user)) {
                return true;
            }
        } else {
            /*
                        $character = optional($book->getManagerAssociatedWithUser($auth_user))->character;

                        if ($character == 'author') {
                            if ($book->comment_count < 1 and
                                $book->user_vote_count < 1)
                                return true;
                        }
            */
            if ($book->isUserCreator($auth_user)) {
                return (boolean) $auth_user->getPermission('delete_self_book');
            } else {
                return (boolean) $auth_user->getPermission('delete_other_user_book');
            }
        }
    }

    /**
     * Восстановить книгу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function restore(User $auth_user, Book $book)
    {
        if (!$book->trashed()) // книга не была удалена, поэтому запрещаем
        {
            return false;
        }

        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if ($book->isPrivate()) {
            // пользователь может восстанавливать свои книги в личной библиотеки
            if ($book->isUserCreator($auth_user)) {
                return true;
            }
        } else {
            if ($auth_user->can('manage', $book)) {
                return true;
            }

            // проверяем может ли он удалять книги из публичной библиотеки

            if ($book->isUserCreator($auth_user)) {
                return (boolean) $auth_user->getPermission('delete_self_book');
            } else {
                return (boolean) $auth_user->getPermission('delete_other_user_book');
            }
        }
    }

    /**
     * Может ли пользователь соеднинить книгу с другой в одну группу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function group(User $auth_user, Book $book)
    {
        if ($book->trashed()) {
            return false;
        }

        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if ($book->isPrivate()) {
            // запрещено объединять книги из личной библиотеки
            return false;
        }

        if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
            return true;
        }

        return (boolean) $auth_user->getPermission('connect_books');
    }

    /**
     * Может ли пользователь разгруппировать книгу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function ungroup(User $auth_user, Book $book)
    {
        if ($book->trashed()) {
            return false;
        }

        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if ($book->isPrivate()) {
            // запрещено объединять книги из личной библиотеки
            return false;
        }

        if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
            return true;
        }

        if ($book->isMainInGroup()) {
            return false;
        }

        return (boolean) $auth_user->getPermission('connect_books');
    }

    /**
     * Может ли пользователь cделать книгу главной в группе
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function make_main_in_group(User $auth_user, Book $book)
    {
        if ($book->isMainInGroup()) {
            return false;
        }

        if ($book->trashed()) {
            return false;
        }

        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if ($book->isPrivate()) {
            // запрещено объединять книги из личной библиотеки
            return false;
        }

        if ($book->getManagerAssociatedWithUser($auth_user)) {
            return true;
        }

        return (boolean) $auth_user->getPermission('connect_books');
    }

    /**
     * Может ли пользователь добавлять к книге ключевые слова
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function addKeywords(User $auth_user, Book $book)
    {
        if ($book->isPrivate()) {
            // если книга из личного облака и если книга добавлена пользователем, то разрешаем
            if ($book->isUserCreator($auth_user)) {
                return true;
            }
        } else {
            if ($book->getManagerAssociatedWithUser($auth_user)) {
                return true;
            }

            if (
                $auth_user->getPermission('book_keyword_add')
                or $auth_user->getPermission('book_keyword_add_new_with_check')
                or $auth_user->getPermission('book_keyword_moderate')
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Может ли пользователь оценивать книгу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function vote(User $auth_user, Book $book)
    {
        if ($book->trashed()) {
            return false;
        }

        return (boolean) $auth_user->getPermission('VoteForBook');
    }

    /**
     * Может ли пользователь удалить оценку книги
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function vote_remove(User $auth_user, Book $book)
    {
        return (boolean) $auth_user->getPermission('VoteForBook');
    }

    /**
     * Может ли пользователь добавлять к книге файлы книг
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function addFiles(User $auth_user, Book $book)
    {
        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if ($book->isForSale()) {
            return false;
        }

        if ($book->isPrivate()) {
            return $book->isUserCreator($auth_user);
        }

        if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
            return true;
        }

        if (!$auth_user->getPermission('access_to_closed_books') and !$book->isDownloadAccess()) {
            return false;
        }

        return $auth_user->getPermission('BookFileAdd');
    }

    /**
     * Может ли пользователь изменить тип доступ к чтению и скачке
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function change_access(User $auth_user, Book $book)
    {
        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if ($book->bought_times_count > 0) {
            if ($book->isRejected()) {
                if ($book->status_changed_at->addDays(config('litlife.book_removed_from_sale_cooldown_in_days'))->isPast()) {
                    return $auth_user->can('author', $book);
                } else {
                    return false;
                }
            }
        }

        if ($book->isPrivate()) {
            return $book->isUserCreator($auth_user);
        }

        if ($book->isReadAccess()) {
            if ($book->bought_times_count > 0) {
                return false;
            }
        }

        if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
            return true;
        }

        return (boolean) $auth_user->getPermission('BookSecretHideSet');
    }

    /**
     * Может ли пользователь читать книгу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    /*
    public function read(User $auth_user, Book $book)
    {
        if ($book->sections_count < 1)
            return false;

        if (!$book->isReadAccess())
        {
            if (@$auth_user->getPermission('access_to_closed_books'))
                return true;
            else
                return false;
        }

        return true;
    }
 */
    /**
     * Может ли пользователь скачать книгу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    /*
    public function download(User $auth_user, Book $book)
    {
        if (!$book->isDownloadAccess())
        {
            if (@$auth_user->getPermission('access_to_closed_books'))
                return true;
            else
                return false;
        }

        return true;
    }
    */

    /**
     * Может ли пользователь комментировать книгу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function commentOn(User $auth_user, Book $book)
    {
        // книга в личном облаке
        if ($book->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($book->isUserCreator($auth_user)) {
                return true;
            } else {
                return false;
            }
        }

        if ($book->comments_closed) {
            return false;
        } else {
            return (boolean) $auth_user->getPermission('add_comment');
        }
    }

    /**
     * Может ли пользователь добавить главу к книге
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function create_section(User $auth_user, Book $book)
    {
        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if (!$book->isPagesNewFormat()) {
            return false;
        }

        // книга в личном облаке
        if ($book->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($book->isUserCreator($auth_user)) {
                return true;
            }
        } else {

            if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
                if (!$book->isEditionDetailsFilled()) {
                    return true;
                }
            }

            if ($book->isUserCreator($auth_user)) {
                return (boolean) $auth_user->getPermission('edit_self_book');
            } else {
                return (boolean) $auth_user->getPermission('edit_other_user_book');
            }
        }
    }

    /**
     * Может ли пользователь добавить вложение в книгу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function create_attachment(User $auth_user, Book $book)
    {
        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        // книга в личном облаке
        if ($book->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($book->isUserCreator($auth_user)) {
                return true;
            }
        }

        if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
            if (!$book->isEditionDetailsFilled()) {
                return true;
            }
        }

        if ($book->isUserCreator($auth_user)) {
            return (boolean) $auth_user->getPermission('edit_self_book');
        } else {
            return (boolean) $auth_user->getPermission('edit_other_user_book');
        }
    }

    /**
     * Может ли пользователь сохранить расположение глав в книге
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function save_sections_position(User $auth_user, Book $book)
    {
        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        // книга в личном облаке
        if ($book->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($book->isUserCreator($auth_user)) {
                return true;
            }
        }

        if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
            if (!$book->isEditionDetailsFilled()) {
                return true;
            }
        }

        if ($book->isUserCreator($auth_user)) {
            return (boolean) $auth_user->getPermission('edit_self_book');
        } else {
            return (boolean) $auth_user->getPermission('edit_other_user_book');
        }
    }

    /**
     * Может ли пользователь переносить главы в сноски и наоборот
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function move_sections_to_notes(User $auth_user, Book $book)
    {
        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        // книга в личном облаке
        if ($book->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($book->isUserCreator($auth_user)) {
                return true;
            }
        }

        if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
            if (!$book->isEditionDetailsFilled()) {
                return true;
            }
        }

        if ($book->isUserCreator($auth_user)) {
            return (boolean) $auth_user->getPermission('edit_self_book');
        } else {
            return (boolean) $auth_user->getPermission('edit_other_user_book');
        }
    }

    /**
     * Может ли пользователь добавить к книге похожую книгу или проголосовать
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function add_similar_book(User $auth_user, Book $book)
    {
        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        // книга в личном облаке
        if ($book->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($book->isUserCreator($auth_user)) {
                return true;
            }
        }

        if ($auth_user->getPermission('BookSimilarVote')) {
            return true;
        }
    }

    /**
     * Может ли пользователь просмотреть список сносок или глав
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function view_section_list(?User $auth_user, Book $book)
    {
        if (!$book->isDescriptionOnly()) {
            if (!$book->parse->isSucceed()) {
                return false;
            }
        }

        // книга в личном облаке
        if ($book->isPrivate()) {

            // разрешаем только если пользователь создатель
            if (!empty($auth_user) and $book->isUserCreator($auth_user)) {
                return true;
            }

            return $this->deny(__('book.access_to_the_book_is_limited'));
        }

        return true;
    }

    /**
     * Может ли пользователь отправить книгу, если у нее провалился парсинг
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function retry_failed_parse(User $auth_user, Book $book)
    {
        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if (!$book->parse->isFailed()) // у книги не провален парсинг
        {
            return false;
        }

        // книга в личном облаке
        if ($book->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($book->isUserCreator($auth_user)) {
                return true;
            }
        } else {
            return @(boolean) $auth_user->getPermission('RetryFailedBookParse');
        }

    }

    /**
     * Убрать обложку книги
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function remove_cover(User $auth_user, Book $book)
    {
        if (empty($book->cover)) {
            return false;
        }

        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if ($book->isForSale()) {
            return false;
        }

        // книга в личном облаке
        if ($book->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($book->isUserCreator($auth_user)) {
                return true;
            }
        }

        if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
            if (!$book->isEditionDetailsFilled()) {
                return true;
            }
        }

        if ($book->isUserCreator($auth_user)) {
            return (boolean) $auth_user->getPermission('delete_self_book');
        } else {
            return (boolean) $auth_user->getPermission('delete_other_user_book');
        }
    }

    /**
     * Может ли пользователь просмотреть книги на модерации
     *
     * @param  User  $auth_user
     */
    public function view_on_moderation(User $auth_user)
    {
        if (@$auth_user->getPermission('CheckBooks')) // пользователю можно проверять книги
        {
            return true;
        }

        return false;
    }

    /**
     * Может ли пользователь просмотреть книги которые объединены в группу
     *
     * @param  User  $auth_user
     */
    public function view_group_books(User $auth_user)
    {
        return true;
    }

    public function watch_activity_logs(User $auth_user, Book $book)
    {
        return @(boolean) $auth_user->getPermission('WatchActivityLogs');
    }

    /**
     * Может ли пользователь видеть техническую информацию о книге
     *
     * @param  User  $auth_user
     */
    public function display_technical_information(User $auth_user)
    {
        return @(boolean) $auth_user->getPermission('display_technical_information');
    }

    /**
     * Можно ли обновить счетчики книги
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function refresh_counters(User $auth_user, Book $book)
    {
        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        // книга в личном облаке
        if ($book->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($book->isUserCreator($auth_user)) {
                return true;
            }
        }

        if ($book->getManagerAssociatedWithUser($auth_user)) {
            return true;
        }

        return @(boolean) $auth_user->getPermission('refresh_counters');
    }

    /**
     * Можно ли открыть комментарии
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function open_comments(User $auth_user, Book $book)
    {
        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if (!$book->comments_closed) {
            return false;
        }

        return @(boolean) $auth_user->getPermission('BookCommentsManage');
    }

    /**
     * Можно ли закрыть комментарии
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function close_comments(User $auth_user, Book $book)
    {
        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if ($book->comments_closed) {
            return false;
        }

        return @(boolean) $auth_user->getPermission('BookCommentsManage');
    }

    /**
     * Есть ли доступ к чтению книги
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function read(?User $auth_user, Book $book)
    {
        if (!$book->isHavePagesToRead()) {
            return false;
        }

        if (!$book->isReadAccess()) {

            if (empty($auth_user)) {
                return false;
            }

            if (!empty($auth_user->getPermission('access_to_closed_books'))) {
                return true;
            }
            /*
                        if ($book->purchases->where('buyer_user_id', $auth_user->id)->first())
                            return true;

                        if (!empty($auth_user) and optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author')
                            return true;
            */
            return $this->deny(__('book.access_to_the_book_is_limited'));
        }

        if ($book->isForSale()) {
            if ($auth_user) {
                if (empty($auth_user->getPermission('shop_enable'))) {
                    return false;
                }
            }

            if (!empty($book->free_sections_count)) {
                return true;
            }

            if (empty($auth_user)) {
                return $this->deny(__('book.paid_part_of_book'));
            }

            if (!empty($auth_user->getPermission('access_to_closed_books'))) {
                return true;
            }

            if ($book->purchases->where('buyer_user_id', $auth_user->id)->first()) {
                return true;
            }

            if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
                return true;
            }

            return $this->deny(__('book.paid_part_of_book'));
        }

        if ($book->isRejected())
        {
            if ($book->purchases->where('buyer_user_id', $auth_user->id)->first()) {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Отображать ли для пользователя кнопку купить книгу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function view_read_button(?User $auth_user, Book $book)
    {
        if (!$book->isHavePagesToRead()) {
            return false;
        }

        if (!$book->isReadAccess()) {

            if (empty($auth_user)) {
                return false;
            }

            if (!empty($auth_user->getPermission('access_to_closed_books'))) {
                return true;
            }

            return $this->deny(__('book.access_to_the_book_is_limited'));
        }

        if ($book->isForSale()) {
            if ($auth_user) {
                if (empty($auth_user->getPermission('shop_enable'))) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Есть ли доступ к скачке книги
     *
     * @param  User  $auth_user
     * @param  Book  $book
     * @throws
     */
    public function view_download_files(?User $auth_user, Book $book)
    {
        if (!empty($auth_user)) {
            if ($auth_user->getPermission('book_file_add_check')) {
                return true;
            }
        }

        if (!$book->isDownloadAccess()) {

            if (empty($auth_user)) {
                return false;
            }

            if (!empty($auth_user->getPermission('access_to_closed_books'))) {
                return true;
            }
            /*
                        if ($book->purchases->where('buyer_user_id', $auth_user->id)->first())
                            return true;

                        if (!empty($auth_user) and optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author')
                            return true;
            */
            return $this->deny(__('book.access_to_the_book_is_limited'));
        }

        if ($book->isForSale()) {
            return true;
        }

        if ($book->isRejected())
        {
            if ($book->purchases->where('buyer_user_id', $auth_user->id)->first()) {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Есть ли доступ к скачке книги
     *
     * @param  User  $auth_user
     * @param  Book  $book
     * @throws
     */
    public function download(?User $auth_user, Book $book)
    {
        if (!empty($auth_user)) {
            if ($auth_user->getPermission('book_file_add_check')) {
                return true;
            }
        }

        if (!$book->isDownloadAccess()) {

            if (empty($auth_user)) {
                return false;
            }

            if (!empty($auth_user->getPermission('access_to_closed_books'))) {
                return true;
            }
            /*
                        if ($book->purchases->where('buyer_user_id', $auth_user->id)->first())
                            return true;

                        if (!empty($auth_user) and optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author')
                            return true;
            */
            return $this->deny(__('book.access_to_the_book_is_limited'));
        }

        if ($book->isForSale()) {
            if (empty($auth_user)) {
                return $this->deny(__('book.you_need_to_purchase_a_book_to_download'));
            }

            if (!empty($auth_user->getPermission('access_to_closed_books'))) {
                return true;
            }

            if ($book->purchases->where('buyer_user_id', $auth_user->id)->first()) {
                return true;
            }

            if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
                return true;
            }

            return $this->deny(__('book.you_need_to_purchase_a_book_to_download'));
        }

        if ($book->isRejected())
        {
            if ($book->purchases->where('buyer_user_id', $auth_user->id)->first()) {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Есть ли доступ к чтению или скачке
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function read_or_download(?User $auth_user, Book $book)
    {
        if (!$book->isHavePagesToRead() and $book->files->count() < 1) {
            return false;
        }

        if (!$book->isReadAccess() and !$book->isDownloadAccess()) {
            if (@$auth_user->getPermission('access_to_closed_books')) {
                return true;
            } else {
                return false;
            }
        } else {
            if (!empty($auth_user) and optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
                return true;
            }
        }

        if ($book->isForSale()) {
            if (empty($auth_user)) {
                return false;
            }

            if ($book->purchases->where('buyer_user_id', $auth_user->id)->first()) {
                return true;
            } else {
                return false;
            }
        }

        if ($book->isRejected())
        {
            if ($book->purchases->where('buyer_user_id', $auth_user->id)->first()) {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Можно ли управлять книгой
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function manage(User $auth_user, Book &$book)
    {
        $manager = $book->getManagerAssociatedWithUser($auth_user);

        if (!empty($manager) and $manager->character == 'author') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Подходит ли книга по возрасту для пользователя
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function pass_age_restriction(?User $auth_user, Book $book)
    {
        if (empty($book->age) or ($book->age < 18)) {
            return true;
        }

        if (!empty($pass_age = request()->cookie('can_pass_age'))) {
            if ($pass_age >= $book->age) {
                return true;
            }
        }

        if (empty($auth_user->born_date)) {
            return false;
        }

        if ($auth_user->born_date->addYears($book->age)->isFuture()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Может ли пользователь прикрепить к книге награду
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function attachAward(User $auth_user, Book $book)
    {
        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        // книга уже распарсена или не нуждается в распарсивании
        if ((!$book->parse->isSucceed()) and (!$book->isDescriptionOnly())) // книга еще не обработана или не нуждается в обработке
        {
            return false;
        }

        if ($book->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($book->isUserCreator($auth_user)) {
                return true;
            }
        }

        return (boolean) $auth_user->getPermission('awards');
    }

    /**
     * Отображать кнопки поиска по названию в яндексе и гугле
     *
     * @param  User  $auth_user
     */
    public function view_se_buttons(User $auth_user)
    {
        if (@$auth_user->getPermission('CheckBooks')) // пользователю можно проверять книги
        {
            return true;
        }

        return false;
    }

    /**
     * Можно ли принудительно преобразовать книгу в новый онлайн формат, чтобы получить доступ к редактированию книги
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function set_as_new_read_online_format(User $auth_user, Book $book)
    {
        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if ($book->isPagesNewFormat()) {
            return false;
        }

        if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
            if (!$book->isEditionDetailsFilled()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Просмотреть удаленные книги
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function view_deleted(User $auth_user)
    {
        if (@$auth_user->getPermission('see_deleted')) {
            return true;
        }

        return false;
    }

    /**
     * Может ли пользователь отменить парсинг файла
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function cancel_parse(User $auth_user, Book $book)
    {
        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if ($book->parse->isSucceed() or $book->parse->isProgress()) {
            return false;
        }

        // книга в личном облаке
        if ($book->isPrivate()) {
            // разрешаем только если пользователь создатель
            if ($book->isUserCreator($auth_user)) {
                return false;
            }
        } else {
            return @(boolean) $auth_user->getPermission('RetryFailedBookParse');
        }

    }

    /**
     * Может ли пользователь изменить настройки продажи книги
     *
     * @param  User  $auth_user
     * @param  Book  $book
     * @return mixed
     * @throws AuthorizationException
     */
    public function change_sell_settings(User $auth_user, Book $book)
    {
        if (!$auth_user->can('use_shop', User::class)) {
            return false;
        }

        if (!$book->parse->isSucceed()) {
            return false;
        }

        if ($book->trashed()) {
            return false;
        }

        if ($book->isRejected()) {
            return $this->deny(__('book.removed_from_sale'));
        }

        $seller = $book->seller();

        if (!empty($seller)) {
            if ($seller->is($auth_user)) {
                return true;
            } else {
                return false;
            }
        } else {
            return $this->deny(__('book.you_must_specify_your_author_page_in_the_writers_field'));
        }
    }

    /**
     * Может ли пользователь продать книгу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     * @throws
     */
    public function sell(User $auth_user, Book $book)
    {
        if (!$auth_user->can('use_shop', User::class)) {
            return false;
        }

        if (!$book->is_si) {
            return false;
        }

        if ($book->is_lp) {
            return false;
        }

        if (!$book->parse->isSucceed()) {
            return false;
        }

        if ($book->trashed()) {
            return false;
        }

        if ($book->isRejected()) {
            return $this->deny(__('book.removed_from_sale'));
        }

        if (!$book->isDownloadAccess() and !$book->isReadAccess()) {
            return false;
        }

        if ($book->ready_status == 'complete_but_publish_only_part' or $book->ready_status == 'not_complete_and_not_will_be') {
            return false;
        }

        if ($book->getAuthorsWithType('writers')->count() > 1) {
            return $this->deny(__('book.we_dont_have_the_opportunity_to_sell_books_with_more_than_one_writer'));
        }

        if ($manager = $book->getManagerAssociatedWithUser($auth_user)) {
            if ($manager->can_sale) {
                if ($book->isUserCreator($auth_user)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return $this->deny(__('book.to_sell_books_you_need_to_request'));
            }
        } else {
            return false;
        }
    }

    /**
     * Может ли пользователь купить книгу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function buy(User $auth_user, Book $book)
    {
        if (!$auth_user->can('use_shop', User::class)) {
            return false;
        }

        if (!$book->isForSale()) {
            return false;
        }

        if (empty($auth_user)) {
            return false;
        }

        if (!$auth_user->can('use_shop', User::class)) {
            return false;
        }

        if (!$book->parse->isSucceed()) {
            return false;
        }

        if ($book->trashed()) {
            return false;
        }

        if (!$book->isDownloadAccess() and !$book->isReadAccess()) {
            return false;
        }

        if (!$book->is_si) {
            return false;
        }

        if ($book->is_lp) {
            return false;
        }

        if ($book->ready_status == 'complete_but_publish_only_part' or $book->ready_status == 'not_complete_and_not_will_be') {
            return false;
        }

        if ($book->free_sections_count >= $book->sections_count) {
            return false;
        }

        $seller_manager = $book->seller_manager();

        if (empty($seller_manager)) {
            return $this->deny(__('book.sales_of_this_book_are_temporarily_disabled'));
        }

        if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
            return $this->deny(__('book.you_can_not_buy_a_book_that_you_sell'));
        } else {
            if ($book->purchases->where('buyer_user_id', $auth_user->id)->first()) {
                return $this->deny(__('book.you_already_buy_this_book'));
            } else {
                return true;
            }
        }
    }

    /**
     * Отображать ли кнопку купить книгу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function buy_button(?User $auth_user, Book $book)
    {
        if ($book->isForSale()) {
            if (!empty($auth_user)) {
                if (!$auth_user->can('use_shop', User::class)) {
                    return false;
                }
            }
        } else {
            return false;
        }

        if (!$book->parse->isSucceed()) {
            return false;
        }

        if ($book->trashed()) {
            return false;
        }

        if (!$book->isReadAccess()) {
            return false;
        }

        if (!$book->is_si) {
            return false;
        }

        if ($book->is_lp) {
            return false;
        }

        if ($book->ready_status == 'complete_but_publish_only_part' or $book->ready_status == 'not_complete_and_not_will_be') {
            return false;
        }

        if ($book->free_sections_count >= $book->sections_count) {
            return false;
        }

        if (empty($auth_user)) {
            return true;
        }

        if ($book->purchases->where('buyer_user_id', $auth_user->id)->first()) {
            return $this->deny(__('book.you_already_buy_this_book'));
        } else {
            return true;
        }
    }

    /**
     * Отображать ли рекламу на страницах чтения книги
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function display_ads(?User $auth_user, Book $book)
    {
        if ($book->isForSale()) {
            return false;
        }

        if (empty($auth_user)) {
            return true;
        }

        if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
            return false;
        }

        if ($book->purchases->where('buyer_user_id', $auth_user->id)->first()) {
            return false;
        }

        return true;
    }

    /**
     * Снять с продажи книгу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     * @throws
     */
    public function remove_from_sale(User $auth_user, Book $book)
    {
        if (!$auth_user->can('use_shop', User::class)) {
            return false;
        }

        if (!$book->isForSale()) {
            return $this->deny();
        }

        if ($manager = $book->getManagerAssociatedWithUser($auth_user)) {
            if ($manager->can_sale) {
                return true;
            } else {
                return $this->deny();
            }
        } else {
            return $this->deny();
        }

        return true;
    }

    /**
     * Является ли пользователь автором книги
     *
     * @param  User  $auth_user
     * @param  Book  $book
     * @throws
     */
    public function author(User $auth_user, Book $book)
    {
        if (optional($book->getManagerAssociatedWithUser($auth_user))->character != 'author') {
            return $this->deny();
        }

        return true;
    }

    /**
     * Может ли пользователь увидеть описание книги если она удалена
     *
     * @param  User  $auth_user
     * @param  Book  $book
     * @throws
     */
    public function see_deleted(?User $auth_user, Book $book)
    {
        if (empty($auth_user)) {
            return false;
        }

        return (boolean) $auth_user->getPermission('see_deleted');
    }

    /**
     * Может ли пользователь заблокировать книги по списку
     *
     * @param  User  $auth_user
     */
    public function blockAccessByList(User $auth_user)
    {
        return (boolean) $auth_user->getPermission('BookSecretHideSet');
    }

    /**
     * Может ли пользователь редактировать год перехода и метку книги общественнго достояния
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function editFieldOfPublicDomain(User $auth_user, Book &$book)
    {
        return (boolean) $auth_user->getPermission('edit_field_of_public_domain');
    }

    /**
     * Может ли пользователь включить доступ к изменению книги
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function enableForbidChangesInBook(User $auth_user, Book &$book)
    {
        if ($book->forbid_to_change) {
            return false;
        }

        return (boolean) $auth_user->getPermission('enable_disable_changes_in_book');
    }

    /**
     * Может ли пользователь выключить доступ к изменению книги
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function disableForbidChangesInBook(User $auth_user, Book &$book)
    {
        if (!$book->forbid_to_change) {
            return false;
        }

        return (boolean) $auth_user->getPermission('enable_disable_changes_in_book');
    }

    /**
     * Может ли пользователь менять данные в поле Писатели
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function changeWritersField(User $auth_user, Book &$book)
    {
        if ($book->isForSale()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Может ли автор заменить этой книгой
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function replaceWithThis(User $auth_user, Book $book)
    {
        if ($book->trashed()) {
            return false;
        }

        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if ($book->isPrivate()) {
            return false;
        }

        if (!$book->isUserCreator($auth_user)) {
            return false;
        }

        if ($book->writers->count() < 1) {
            return false;
        }

        if ($book->isInGroup() and $book->isNotMainInGroup()) {
            return false;
        }

        if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
            return true;
        }

        return false;
    }

    /**
     * Может ли автор заменить эту книгу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function replaceThis(User $auth_user, Book $book)
    {
        if ($book->trashed()) {
            return false;
        }

        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if ($book->isPrivate()) {
            return false;
        }

        if ($book->isUserCreator($auth_user)) {
            return false;
        }

        if ($book->writers->count() < 1) {
            return false;
        }

        if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
            return true;
        }

        return false;
    }

    /**
     * Может ли создать обработку текста
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function createTextProcessing(User $auth_user, Book $book)
    {
        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if (!$book->isPagesNewFormat()) {
            return false;
        }

        if ($auth_user->getPermission('create_text_processing_books')) {
            return true;
        }
        /*
                if ($book->isPrivate()) {
                    if ($book->isUserCreator($auth_user))
                        return true;
                }

                if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author')
                    return true;
        */
        return false;
    }

    /**
     * Может ли просмотреть обработки текста
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function viewTextProcessing(User $auth_user, Book $book)
    {
        if (!$book->isPagesNewFormat()) {
            return false;
        }

        if ($auth_user->getPermission('create_text_processing_books')) {
            return true;
        }

        /*
                if ($book->isPrivate()) {
                    if ($book->isUserCreator($auth_user))
                        return true;
                }

                if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author')
                    return true;
        */
        return false;
    }

    /**
     * Может ли пользователь удалить у книги все файлы, главы, сноски и изображения
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function deletingOnlineReadAndFiles(User $auth_user, Book $book)
    {
        if ($auth_user->getPermission('deleting_online_read_and_files')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Может ли пользователь изменить поля си, ли или реквизиты печатного издания
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function editSiLpPublishFields(User $auth_user, Book $book)
    {
        if ($book->isAccepted()) {
            if ($book->isUserVerifiedAuthorOfBook($auth_user)) {
                if ($book->isEditionDetailsFilled()) {
                    return false;
                }

                if ($book->is_si) {
                    return false;
                }

                if ($book->is_lp) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Может ли пользователь пожаловаться на книгу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function complain(User $auth_user, Book $book)
    {
        if ($book->isPrivate()) {
            return false;
        }

        if ($book->trashed()) {
            return false;
        }

        return (boolean) $auth_user->getPermission('Complain');
    }

    /**
     * Может ли пользователь увидеть сообщение о том как начать редактировать текст книги в старом формате
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function see_a_message_about_how_to_start_editing_the_text_of_a_book_in_the_old_format(User $auth_user, Book $book)
    {
        if ($book->isPagesNewFormat()) {
            return false;
        }

        if (!$book->isCanChange($auth_user)) {
            return false;
        }

        if ($book->parse->isWait()) {
            return false;
        }

        if ($book->parse->isProgress()) {
            return false;
        }

        if (!$book->parse->isSucceed()) {
            return false;
        }

        if ($book->isPrivate()) {
            if ($book->isUserCreator($auth_user)) {
                return true;
            }
        } else {

            if (optional($book->getManagerAssociatedWithUser($auth_user))->character == 'author') {
                if (!$book->isEditionDetailsFilled()) {
                    return true;
                }
            }

            if ($book->isUserCreator($auth_user)) {
                return (boolean) $auth_user->getPermission('edit_self_book');
            } else {
                return (boolean) $auth_user->getPermission('edit_other_user_book');
            }
        }
    }

    /**
     * Может ли пользователь пожаловаться на книгу
     *
     * @param  User  $auth_user
     * @param  Book  $book
     */
    public function addToCollection(User $auth_user, Book $book)
    {
        if ($book->isPrivate()) {
            return false;
        }

        return true;
    }
}
