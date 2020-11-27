<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Auth::routes();
Route::get('/faq', 'OtherController@faq')->name('faq');
Route::get('/', 'HomeController@index')->name('home');
Route::get('/search', 'SearchController@results')->name('search');
Route::get('/search/google', 'SearchController@google')->name('search.google');
Route::get('/latest_books', 'HomeController@latest_books')->name('home.latest_books');
Route::get('/popular_books/{period?}', 'HomeController@popular_books')->name('home.popular_books')->where('period', '(day|week|month|quarter|year)');
Route::get('/latest_comments', 'HomeController@latest_comments')->name('home.latest_comments');
Route::get('/latest_posts', 'HomeController@latest_posts')->name('home.latest_posts');
Route::get('/latest_wall_posts', 'HomeController@latest_wall_posts')->name('home.latest_wall_posts');
Route::get('/test', 'TestController@index')->name('test');
Route::get('/test2', 'TestController@test2')->name('test2');
Route::post('/test', 'TestController@test_post')->name('test.post');
Route::get('/sidebar/show', 'OtherController@sidebarShow')->name('sidebar.show');
Route::get('/sidebar/hide', 'OtherController@sidebarHide')->name('sidebar.hide');
Route::get('/rules', 'TextBlockController@rules')->name('rules');
Route::get('/for_rights_owners', 'TextBlockController@forRightsOwners')->name('for_rights_owners');
Route::get('/rules_publish_books', 'TextBlockController@rulesPublishBooks')->name('rules_publish_books');
Route::resource('comments', 'CommentController', ['only' => ['index', 'show']]);
Route::get('/genres/search', 'GenreController@search')->name('genres.search');
Route::get('/genres/all_for_select2', 'GenreController@allForSelect2');
Route::get('/genres/select_list', 'GenreController@selectList');
Route::resource('genres', 'GenreController', [
	'names' => [
		'index' => 'genres'
	]
]);

Route::get('/genres/{genre}', 'GenreController@show')->name('genres.show');

Route::get('sequences', 'SequenceListController@index');

Route::resource('users', 'UserController', [
	'names' => [
		'show' => 'profile'
	]
])->except(['store', 'edit', 'update', 'destroy']);

Route::get('users/{user}', 'UserController@show')->where('user', '[A-z0-9]+')->name('profile');
Route::resource('books', 'BookController', ['only' => ['index', 'show']]);
Route::get('books', 'BookListController@index')->name('books');
Route::get('last_book', 'BookListController@last_book');
Route::resource('authors', 'AuthorController', ['only' => ['index', 'show']]);
Route::resource('smiles', 'SmileController');
Route::get('/books/{book}/read', 'BookReadOldController@show')->name('books.old.page');
Route::get('/books/{book}/read/images/{name}', 'BookReadOldController@binary')->name('books.old.image')->where('book', '[0-9]+');
Route::get('/books/{book}/read/online', 'BookController@readRedirect')->name('books.read.online');
Route::get('/books/{book}/cover', 'BookController@cover')->name('books.cover.show');
Route::get('/books/{book}/sections/list_go_to', 'SectionController@listGoToChapter')->name('books.sections.list_go_to');

Route::get('/emails/{email}/sendConfirmToken', 'UserEmailController@sendConfirmToken')->name('email.send_confirm_token');
Route::get('/emails/{email}/confirm/{token}', 'UserEmailController@confirm')->name('email.confirm');
Route::get('/emails/{email}/notice/disable/', 'UserEmailController@notice_disable')->name('email.notice_disable');
Route::get('/books_group/{group}', 'BookListController@group')->name('books.group.index');
Route::get('/qrcode', 'OtherController@qrcode')->name('qrcode');
Route::get('/drip', 'DripController@drip')->name('drip');
Route::get('keywords/search', 'BookKeywordController@search')->name('books.keywords.search');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/collections/{collection}/comments', 'CollectionController@comments')->name('collections.comments');
Route::get('/collections/{collection}/books', 'CollectionController@books')->name('collections.books');

Route::get('/users/where/nick', 'UserController@searchUserWithNick')->name('users.where.nick');

Route::get('authors_search', 'AuthorController@search');
Route::any('authors/search', 'AuthorController@search')->name('authors.search');

Route::get('/books/search', 'BookController@search')->name('books.search');

Route::get('/authors/how_to_start_selling_books', 'AuthorSaleRequestController@howToStartSellingBooks')->name('authors.how_to_start_selling_books');

Route::group(['middleware' => ['guest']], function () {
	Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login.form');
	Route::post('/login', 'Auth\LoginController@login')->name('login')->middleware('throttle:30,1');
	// middleware guest start
	Route::get('invitations', 'InvitationController@create')->name('invitation');
	Route::post('invitations', 'InvitationController@store')->name('invitation.store');
	// {token}  обязательный параметр, который будет передан в метод контроллера
	Route::get('invitations/accept/{token}', 'InvitationController@accept')->name('users.registration');
	Route::post('invitations/user/{token}', 'InvitationController@user')->name('users.store');
	// Password Reset Routes...
	Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
	Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
	Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset_form');
	Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');
});

Route::group(['middleware' => ['auth']], function () {

	Route::get('books/{book}/collections', 'BookController@collections')->name('books.collections.index');
	Route::get('books/{book}/collections/search', 'BookController@collectionSearch')->name('books.collections.search');
	Route::get('books/{book}/collections/create', 'BookController@collectionCreate')->name('books.collections.create');
	Route::get('books/{book}/collections/{collection}/selected', 'BookController@collectionSelected')->name('books.collections.selected');
	Route::post('books/{book}/collections', 'BookController@collectionStore')->name('books.collections.store');

	Route::resource('collections', 'CollectionController', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);

	Route::get('/collections/{collection}/delete/confirmation', 'CollectionController@deleteConfirmation')->name('collections.delete.confirmation');
	Route::get('/collections/{collection}/favorite/toggle', 'CollectionController@toggleToFavorites')->name('collections.favorite.toggle');
	Route::get('/collections/{collection}/books/select', 'CollectionController@booksSelect')->name('collections.books.select');
	Route::post('/collections/{collection}/books/attach', 'CollectionController@booksAttach')->name('collections.books.attach');
	Route::get('/collections/{collection}/books/{book}/detach', 'CollectionController@booksDetach')->name('collections.books.detach');
	Route::get('/collections/{collection}/books/{book}/edit', 'CollectionController@collectedBookEdit')->name('collections.books.edit');
	Route::post('/collections/{collection}/books/{book}/update', 'CollectionController@collectedBookUpdate')->name('collections.books.update');
	Route::get('/collections/{collection}/event_notification_subcriptions/toggle', 'CollectionController@eventNotificationSubcriptionsToggle')
		->name('collections.event_notification_subcriptions.toggle');
	Route::get('/users/{user}/collections/created', 'UserController@createdCollections')->name('users.collections.created');
	Route::get('/collections/{collection}/users', 'CollectionController@users')->name('collections.users.index');
	Route::get('/users/{user}/collections/favorite', 'UserController@favoriteCollections')->name('users.collections.favorite');
	Route::get('/collections/{collection}/users/create', 'CollectionController@createUser')->name('collections.users.create');
	Route::post('/collections/{collection}/users', 'CollectionController@storeUser')->name('collections.users.store');
	Route::get('/collections/{collection}/users/{user}/edit', 'CollectionController@editUser')->name('collections.users.edit');
	Route::patch('/collections/{collection}/users/{user}', 'CollectionController@updateUser')->name('collections.users.update');
	Route::get('/collections/{collection}/users/{user}/delete', 'CollectionController@deleteUser')->name('collections.users.delete');

	Route::get('/collections/{collection}/books/search/list', 'CollectionController@searchList')->name('collections.books.list');
	Route::get('/collections/books/selected/{book}/item', 'CollectionController@booksSelectedItem')->name('collections.books.selected.item');
	Route::get('financial_statistics', 'FinancialStatisticController@index')->name('financial_statistic.index');
	Route::get('financial_statistics/all_transactions', 'FinancialStatisticController@allTransactionHisory')->name('financial_statistic.all_transactions');
	Route::get('financial_statistics/purchases', 'FinancialStatisticController@purchases')->name('financial_statistic.purchases');

	Route::get('purchases/{purchase}/cancel', 'UserPurchaseController@cancel')->name('purchases.cancel');

	Route::get('activity_logs', 'ActivityLogController@index');
	Route::get('/book_keywords/on_moderation', 'BookKeywordController@onModeration')->name('book_keywords.on_moderation');
	Route::resource('images', 'ImageController', ['only' => ['create', 'destroy']]);
	Route::post('/images/', ['uses' => 'ImageController@store', 'nocsrf' => true])->name('images.store');
	Route::resource('authors', 'AuthorController', ['except' => ['index', 'show']]);
	Route::get('/authors/{author}/photo', 'AuthorController@photoShow')->name('authors.photo');
	Route::get('/authors/{author}/delete', 'AuthorController@delete')->name('authors.delete');
	Route::get('/authors/{author}/read_status/{code}', 'AuthorController@read_status')->where('code', '(' . implode('|', \App\Enums\ReadStatus::getValues()) . ')');
	Route::get('/authors/{author}/toggle_my_library', 'AuthorController@toggle_my_library');
	Route::get('/authors/{author}/favorites/toggle', 'AuthorController@toggle_my_library')->name('authors.favorites.toggle');
	//Route::get('/authors/{author}/moderator_request', 'AuthorModeratorController@show');
	Route::get('/managers_on_check', 'ManagerController@on_check')->name('managers.on_check');
	Route::get('/authors/{author}/managers', 'AuthorManagerController@index')->name('authors.managers');
	Route::post('/authors/{author}/managers', 'AuthorManagerController@store')->name('authors.managers.store');

	Route::get('/authors/{author}/verification/request', 'AuthorManagerController@verificationRequest')->name('authors.verification.request');
	Route::post('/authors/{author}/verification/request', 'AuthorManagerController@verificationRequestSave')->name('authors.verification.request_save');

	Route::get('/authors/{author}/editor/request', 'AuthorManagerController@editorRequest')->name('authors.editor.request');
	Route::post('/authors/{author}/editor/request', 'AuthorManagerController@editorRequestSave')->name('authors.editor.request_save');

	Route::get('/verifications/{manager}', 'AuthorManagerController@show')->name('verifications.show');

	Route::get('/authors/{author}/sales/request', 'AuthorManagerController@salesRequestForm')->name('authors.sales.request');
	Route::post('/authors/{author}/sales/request', 'AuthorManagerController@salesRequestStore')->name('authors.sales.store');
	Route::get('/authors_sales_requests', 'AuthorSaleRequestController@index')->name('authors.sales_requests.index');
	Route::get('/authors_sales_requests/{request}', 'AuthorSaleRequestController@show')->name('authors.sales_requests.show');
	Route::get('/authors_sales_requests/{request}/accept', 'AuthorSaleRequestController@accept')->name('authors.sales_requests.accept');
	Route::post('/authors_sales_requests/{request}/reject', 'AuthorSaleRequestController@reject')->name('authors.sales_requests.reject');
	Route::get('/authors_sales_requests/{request}/start_review', 'AuthorSaleRequestController@startReview')->name('authors.sales_requests.start_review');
	Route::get('/authors_sales_requests/{request}/stop_review', 'AuthorSaleRequestController@stopReview')->name('authors.sales_requests.stop_review');
	Route::get('/managers/{manager}/destroy', 'ManagerController@destroy')->name('managers.destroy');
	Route::get('/managers/{manager}/approve', 'ManagerController@approve')->name('managers.approve');
	Route::get('/managers/{manager}/decline', 'ManagerController@decline')->name('managers.decline');
	Route::get('/managers/{manager}/start_review', 'ManagerController@startReview')->name('managers.start_review');
	Route::get('/managers/{manager}/stop_review', 'ManagerController@stopReview')->name('managers.stop_review');
	Route::get('/authors/{author}/sales/disable', 'AuthorManagerController@salesDisable')->name('authors.sales.disable');
	/*
		Route::post('/authors/{author}/moderators/save', 'AuthorModeratorController@save');
		Route::get('/moderator_requests/{request}/approve', 'AuthorModeratorController@approve');
		Route::get('/moderator_requests/{request}/decline', 'AuthorModeratorController@decline');
		Route::get('/authors/{author}/moderators', 'AuthorModeratorController@index');
		Route::post('/authors/{author}/moderators/', 'AuthorModeratorController@store');
		Route::get('/authors/{author}/moderators/{moderator}', 'AuthorModeratorController@destroy');
		*/
	Route::get('authors/merge', 'AuthorController@mergeForm')->name('authors.merge');
	Route::post('authors/merge', 'AuthorController@merge')->name('authors.merge.store');
	Route::get('authors/{author}/all_links_to_books', 'AuthorController@allLinksToBooks')->name('authors.books.files.urls');
	Route::get('authors/{author}/refresh_counters', 'AuthorController@refreshCounters')->name('authors.refresh_counters');
	Route::get('authors/{author}/activity_logs', 'AuthorController@activity_logs')->name('authors.activity_logs');
	Route::get('authors/{author}/make_accepted', 'AuthorController@makeAccepted')->name('authors.make_accepted');
	Route::get('authors/{author}/books/close_access', 'AuthorController@booksCloseAccess')->name('authors.books.close_access');

	// Сообщения о повторах авторов
	Route::get('/news', 'BlogController@news')->name('news');
	Route::resource('author_repeats', 'AuthorRepeatController');
	Route::get('/author_repeats/{author_repeat}/delete', 'AuthorRepeatController@delete')->name('author_repeats.delete');

	Route::resource('users', 'UserController')->only(['edit', 'update', 'destroy']);
	Route::get('/users/{user}/groups', 'UserController@groupEdit')->name('users.groups.edit');
	Route::patch('/users/{user}/groups', 'UserController@groupUpdate')->name('users.groups.update');
	Route::get('/users/{user}/votes', 'BookListController@votes')->name('users.votes');
	Route::get('/users', 'UserListController@index')->name('users');
	Route::get('/users/{user}/books/readed/comments', 'CommentListController@user_readed_books')->name('users.books.readed.comments'); // 2
	Route::get('/users/{user}/refresh_counters', 'UserController@refreshCounters')->name('users.refresh_counters');
	Route::get('/users/{user}/friends', 'UserListController@friends')->name('users.friends');
	Route::get('/users/{user}/subscriptions', 'UserListController@subscriptions')->name('users.subscriptions');
	Route::get('/users/{user}/subscribers', 'UserListController@subscribers')->name('users.subscribers');
	Route::get('/users/{user}/blacklists', 'UserListController@blacklists')->name('users.blacklists');
	Route::get('/users/{user}/subscriptions/comments', 'CommentListController@subscriptions')->name('users.subscriptions.comments');
	Route::get('/users/{user}/books', 'BookListController@userLibrary')->name('users.books');
	Route::get('/users/{user}/books/created', 'BookListController@userCreated')->name('users.books.created');
	Route::get('/users/{user}/authors', 'AuthorListController@userLibrary')->name('users.authors');
	Route::get('/users/{user}/authors/created', 'AuthorListController@userCreated')->name('users.authors.created');
	Route::get('/users/{user}/authors/books', 'BookListController@favoriteAuthorsBooks')->name('users.authors.books');
	Route::get('/users/{user}/sequences', 'SequenceListController@userLibrary')->name('users.sequences');
	Route::get('/users/{user}/sequences/created', 'SequenceListController@userCreated')->name('users.sequences.created');
	Route::get('/users/{user}/books/purchased', 'BookListController@purchased')->name('users.books.purchased');
	Route::get('/users/{user}/books/updates', 'BookListController@updates')->name('users.books.updates'); // 2
	Route::get('/users/{user}/books/readed', 'BookListController@user_readed')->name('users.books.readed'); // 2
	Route::get('/users/{user}/books/read_later', 'BookListController@user_read_later')->name('users.books.read_later'); // 3
	Route::get('/users/{user}/books/read_now', 'BookListController@user_read_now')->name('users.books.read_now'); // 4
	Route::get('/users/{user}/books/read_not_complete', 'BookListController@user_read_not_complete')->name('users.books.read_not_complete'); // 5
	Route::get('/users/{user}/books/not_read', 'BookListController@user_not_read')->name('users.books.not_read'); // 6
	Route::get('/users/{user}/authors/readed', 'AuthorListController@user_readed')->name('users.authors.readed'); // 2
	Route::get('/users/{user}/authors/read_later', 'AuthorListController@user_read_later')->name('users.authors.read_later'); // 3
	Route::get('/users/{user}/authors/read_now', 'AuthorListController@user_read_now')->name('users.authors.read_now'); // 4
	Route::get('/users/{user}/authors/read_not_complete', 'AuthorListController@user_read_not_complete')->name('users.authors.read_not_complete'); // 5
	Route::get('/users/{user}/authors/not_read', 'AuthorListController@user_not_read')->name('users.authors.not_read'); // 6
	Route::get('/users/{user}/books/comments', 'CommentListController@user')->name('users.books.comments');
	Route::get('/users/{user}/posts', 'PostListController@user')->name('users.posts');
	Route::get('/users/{user}/topics', 'TopicListController@user')->name('users.topics');
	Route::get('/users/{user}/subscribe', 'UserRelationController@subscribe')->name('users.subscribe');
	Route::get('/users/{user}/unsubscribe', 'UserRelationController@unsubscribe')->name('users.unsubscribe');
	Route::get('/users/{user}/block', 'UserRelationController@block')->name('users.block');
	Route::get('/users/{user}/unblock', 'UserRelationController@unblock')->name('users.unblock');
	Route::get('/users/{user}/moderation/add', 'UserController@addOnModeration')->name('users.moderations.add');
	Route::get('/users/{user}/moderation/remove', 'UserController@removeFromModeration')->name('users.moderations.remove');
	Route::get('/users/{user}/suspend', 'UserController@suspend')->name('users.suspend');
	Route::get('/users/{user}/unsuspend', 'UserController@unsuspend')->name('users.unsuspend');
	Route::get('/users/{user}/delete', 'UserController@delete')->name('users.delete');
	Route::get('/users/{user}/restore', 'UserController@restore')->name('users.restore');
	Route::resource('users.emails', 'UserEmailController')->except('show');
	Route::get('/users/{user}/emails/{email}/show', 'UserEmailController@show')->name('users.emails.show');
	Route::get('/users/{user}/emails/{email}/hide', 'UserEmailController@hide')->name('users.emails.hide');
	Route::get('/users/{user}/emails/{email}/rescue', 'UserEmailController@rescue')->name('users.emails.rescue');
	Route::get('/users/{user}/emails/{email}/unrescue', 'UserEmailController@unrescue')->name('users.emails.unrescue');
	Route::get('/users/{user}/emails/{email}/notifications/enable', 'UserEmailController@notificationsEnable')->name('users.emails.notifications.enable');
	Route::get('/users/{user}/emails/{email}/delete', 'UserEmailController@destroy')->name('users.emails.delete');
	Route::get('/users_on_moderation', 'UserListController@usersOnModeration')->name('users.on_moderation');
	Route::get('/users/{user}/auth_fails', 'UserController@authFails')->name('users.auth_fails');
	Route::get('/users/{user}/auth_logs', 'UserController@authLogs')->name('users.auth_logs');
	Route::get('/users/{user}/activity_logs', 'UserController@activity_logs')->name('users.activity_logs');
	Route::get('/all_users_auth_logs', 'UserController@allAuthLogs')->name('all_users_auth_logs');
	Route::get('/users/{user}/images', 'UserController@images')->name('users.images.index');
	Route::get('/users/{user}/set_miniature', 'UserController@setMiniature')->name('users.set_miniature');
	Route::get('/users/{user}/inbox', 'MessageController@inbox')->name('users.inbox');
	Route::resource('messages', 'MessageController')->only(['show', 'edit', 'update', 'destroy']);
	Route::get('users/{user}/messages', 'MessageController@index')->name('users.messages.index');
	Route::post('users/{user}/messages', 'MessageController@store')->name('users.messages.store');
	Route::get('users/{user}/settings/allowance', 'UserSettingController@allowance')->name('allowance');
	Route::patch('users/{user}/settings/allowance', 'UserSettingController@allowanceUpdate')->name('allowance.patch');
	Route::get('users/{user}/settings/notifications', 'UserSettingController@emailDelivery')->name('users.settings.notifications');
	Route::patch('users/{user}/settings/notifications', 'UserSettingController@emailDeliveryUpdate')->name('users.settings.notifications.update');
	Route::get('users/{user}/settings/email_delivery', 'UserSettingController@emailDelivery')->name('email_delivery');
	Route::patch('users/{user}/settings/email_delivery', 'UserSettingController@emailDeliveryUpdate')->name('users.settings.email_delivery.update');
	Route::get('/users/{user}/genre/blacklist', 'UserSettingController@genreBlacklist')->name('genre_blacklist');
	Route::post('/users/{user}/genre/blacklist', 'UserSettingController@genreBlacklistUpdate')->name('genre_blacklist.update');
	Route::get('/read_style', 'UserSettingController@readStyleRedirect')->name('settings.read_style');
	Route::get('/users/{user}/settings/read_style', 'UserSettingController@readStyle')->name('users.settings.read_style');
	Route::post('/users/{user}/settings/read_style', 'UserSettingController@readStyleUpdate')->name('users.settings.read_style.update');
	Route::get('/users/{user}/settings/other', 'UserSettingController@other')->name('settings.other');
	Route::post('/users/{user}/settings/other', 'UserSettingController@otherUpdate')->name('settings.other.update');
	Route::get('/users/{user}/settings/other', 'UserSettingController@other')->name('settings.other');
	Route::get('/users/{user}/settings/site_appearance', 'UserSettingController@siteAppearance')->name('users.settings.site_appearance');
	Route::post('/users/{user}/settings/site_appearance', 'UserSettingController@siteAppearanceUpdate')->name('users.settings.site_appearance.update');
	Route::get('/users/{user}/wallet/payment_details', 'UserWalletController@paymentDetails')->name('users.wallet.payment_details');
	Route::post('/users/{user}/wallet/payment_details', 'UserWalletController@paymentDetailsSave')->name('users.wallet.payment_details.save');
	Route::get('/users/{user}/wallet/withdrawal', 'UserWalletController@orderWithdrawal')->name('users.wallet.withdrawal');
	Route::post('/users/{user}/wallet/withdrawal', 'UserWalletController@saveWithdrawal')->name('users.wallet.withdrawal.save');
	Route::get('/users/{user}/wallet', 'UserWalletController@wallet')->name('users.wallet');
	Route::get('/users/{user}/wallet/deposit', 'UserWalletController@deposit')->name('users.wallet.deposit');
	Route::post('/users/{user}/wallet/deposit', 'UserWalletController@depositPay')->name('users.wallet.deposit.pay');

	Route::get('/users/{user}/wallet/transfer', 'UserWalletController@orderTransfer')->name('users.wallet.transfer');
	Route::post('/users/{user}/wallet/transfer', 'UserWalletController@saveTransfer')->name('users.wallet.transfer.save');

	Route::get('/users/{user}/transactions/{transaction}/pay', 'UserWalletController@payWaitedTransaction')->name('users.transaction.pay');
	Route::get('/users/{user}/transactions/{transaction}/cancel', 'UserWalletController@transactionCancel')->name('users.transaction.cancel');

	Route::get('/users/{user}/referred/users', 'UserListController@referredUsers')->name('users.referred.users');

	Route::get('/users/{user}/ban', 'UserController@ban')->name('users.ban');

	Route::get('/books/search/settings/store', 'UserSearchSettingController@store')->name('users.books.search.settings.store');

	Route::get('books/{book}/sections/{parentSectionId}/destroy/', 'SectionController@destroy');
	Route::any('books/{book}/sections/save_position', 'SectionController@savePosition')->name('books.sections.save_position');
	Route::get('books/{book}/sections/loadList', 'SectionController@loadList');
	Route::post('books/{book}/sections/move_to_notes', 'SectionController@moveToNote')->name('books.sections.move_to_notes');
	Route::post('books/{book}/sections/move_to_chapters', 'SectionController@moveToChapters')->name('books.sections.move_to_chapters');
	Route::resource('books.sections', 'SectionController', ['except' => ['index', 'show']]);
	Route::get('books/{book}/sections/{section}/delete', 'SectionController@destroy')->name('books.sections.delete');
	Route::get('books/{book}/activity_logs', 'BookController@activity_logs')->name('books.activity_logs');
	Route::get('books/{book}/notes/loadList', 'NoteController@loadList');
	Route::post('books/{book}/notes/save_position', 'NoteController@savePosition')->name('books.notes.save_position');
	Route::post('books/{book}/notes/move_to_sections', 'NoteController@moveToSections')->name('books.notes.move_to_sections');
	Route::resource('books.notes', 'NoteController', ['except' => ['index', 'show']]);
	Route::get('/books/{book}/similar', 'BookListController@similar')->name('books.similar');
	Route::get('books/{book}/stop_reading', 'BookController@stopReading')->name('books.stop_reading');
	Route::resource('books.keywords', 'BookKeywordController');
	Route::resource('books', 'BookController', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);

	Route::get('/books/{book}/create/description', 'BookController@description')->name('books.create.description');
	Route::get('/books/{book}/create/complete', 'BookController@createComplete')->name('books.create.complete');

	Route::get('sequences/{id}/book_edit', 'BookController@edit_sequence_item');
	Route::get('books/trashed', 'BookListController@trashed')->name('books.trashed');
	Route::get('books/{book}/access', 'BookController@accessEdit')->name('books.access.edit');
	Route::post('books/{book}/access', 'BookController@accessSave')->name('books.access.save');
	Route::get('books/{book}/editions', 'BookListController@editions')->name('books.editions.index');
	Route::get('books/{book}/editions/edit', 'BookGroupController@editionsEdit')->name('books.editions.edit');

	Route::get('/books/disable_access_by_list', 'BookController@enterBlockingList')->name('books.access_by_list.enter');
	Route::post('/books/disable_access_by_list', 'BookController@disableAccessByList')->name('books.access_by_list.disable');
	/*
		Route::get('books_publishers', 'BookController@publishers');
		Route::get('books_publish_city', 'BookController@publish_city');
	*/
	Route::resource('books.attachments', 'AttachmentController')->only(['index', 'store']);
	Route::delete('books/{book}/attachments/{id}/delete', 'AttachmentController@delete')->name('books.attachments.delete');
	Route::get('books/{book}/keywords/{keyword}/vote/{vote}', 'BookKeywordController@vote')->where('vote', '(1|-1)')->name('books.keywords.vote');
	Route::get('books/{book}/keywords/{keyword}/approve', 'BookKeywordController@approve')->name('books.keywords.accept');
	Route::get('books/{book}/vote/{vote}', 'BookController@vote')->where('vote', '(1|2|3|4|5|6|7|8|9|10)')->name('books.vote');
	Route::get('books/{book}/vote/remove', 'BookController@voteRemove')->name('books.votes.delete');
	Route::get('books/{book}/users/readed', 'UserListController@usersRead')->name('books.readed');
	Route::get('books/{book}/users/read_later', 'UserListController@usersWantToRead')->name('books.read_later');
	Route::get('books/{book}/users/read_now', 'UserListController@usersReadNow')->name('books.read_now');
	Route::get('books/{book}/users/read_not_complete', 'UserListController@usersCantRead')->name('books.read_not_complete');
	Route::get('books/{book}/users/votes', 'UserListController@usersBookVotes')->name('books.votes');
	Route::get('books/{book}/toggle_my_library', 'BookController@toggle_my_library')->name('books.favorites.toggle');
	Route::get('books/{book}/read_status/{code}', 'BookController@read_status')->name('books.read_status.store')->where('code', '(' . implode('|', \App\Enums\ReadStatus::getValues()) . ')');
	Route::get('books/{book}/similar_vote/{otherBook}/{vote}', 'BookController@voteForSimilar')->name('books.similar.vote');
	Route::post('books/{book}/similars/add', 'BookController@addSimilarBook')->name('books.similar.create');
	Route::get('books/{book}/delete/form', 'BookController@deleteForm')->name('books.delete.form');
	Route::get('books/{book}/delete', 'BookController@delete')->name('books.delete');
	Route::get('books/{book}/restore', 'BookController@restore')->name('books.restore');
	Route::get('books/{book}/close_access', 'BookController@close_access')->name('books.close_access');
	Route::get('books/{book}/make_accepted/rules', 'BookController@makeAcceptedRules')->name('books.make_accepted.rules');
	Route::get('books/{book}/make_accepted', 'BookController@publish')->name('books.make_accepted');
	Route::get('books/{book}/add_for_review/rules', 'BookController@addForReviewRules')->name('books.add_for_review.rules');
	Route::get('books/{book}/add_for_review', 'BookController@publish')->name('books.add_for_review');
	Route::get('books/{book}/publish', 'BookController@publish')->name('books.publish');
	Route::get('books/{book}/add_to_private', 'BookController@addToPrivateForm')->name('books.add_to_private.form');
	Route::post('books/{book}/add_to_private', 'BookController@addToPrivate')->name('books.add_to_private');
	Route::get('books/{book}/check', 'BookController@check')->name('books.check');
	Route::get('books/{book}/retry_failed_parse', 'BookController@retryFailedParse')->name('books.retry_failed_parse');
	Route::get('books/{book}/cancel_parse', 'BookController@cancelParse')->name('books.cancel_parse');
	Route::get('books/{book}/refresh_counters', 'BookController@refreshCounters')->name('books.refresh_counters');
	Route::get('books/{book}/open_comments', 'BookController@openComments')->name('books.open_comments');
	Route::get('books/{book}/close_comments', 'BookController@closeComments')->name('books.close_comments');
	Route::get('books/{book}/forbid_changes/enable', 'BookController@enableForbidChangesInBook')->name('books.forbid_changes.enable');
	Route::get('books/{book}/forbid_changes/disable', 'BookController@disableForbidChangesInBook')->name('books.forbid_changes.disable');
	Route::get('books/{book}/deleting_online_read_and_files', 'BookController@deletingOnlineReadAndFiles')->name('books.deleting_online_read_and_files');

	Route::get('books/{book}/ratings/date/edit', 'BookController@editDateOfRating')->name('books.ratings.date.edit');
	Route::patch('books/{book}/ratings/date', 'BookController@updateDateOfRating')->name('books.ratings.date.update');

	Route::get('books/{book}/read_status/date/edit', 'BookController@editDateOfReadStatus')->name('books.read_status.date.edit');
	Route::patch('books/{book}/read_status/date', 'BookController@updateDateOfReadStatus')->name('books.read_status.date.update');

	Route::get('books/{book}/text_processings', 'BookTextProcessingController@index')->name('books.text_processings.index');
	Route::get('books/{book}/text_processings/create', 'BookTextProcessingController@create')->name('books.text_processings.create');
	Route::post('books/{book}/text_processings', 'BookTextProcessingController@store')->name('books.text_processings.store');

	Route::get('books/{book}/replace_book_created_by_another_user', 'BookController@listOfBooksAddedByOtherUsersForm')
		->name('books.replace_book_created_by_another_user.form');
	Route::post('books/{book}/replace_book_created_by_another_user', 'BookController@replaceBookCreatedByAnotherUser')
		->name('books.replace_book_created_by_another_user');

	Route::get('books/{book}/set_as_new_read_online_format', 'BookController@setAsNewReadOnlineFormat')->name('books.set_as_new_read_online_format');
	Route::get('books/{book}/sales', 'BookController@salesEdit')->name('books.sales.edit');
	Route::post('books/{book}/sales', 'BookController@salesSave')->name('books.sales.save');
	Route::get('books/{book}/remove_from_sale', 'BookController@removeFromSale')->name('books.remove_from_sale');
	Route::get('books/{book}/buy', 'BookController@buy')->name('books.buy');
	Route::get('books/{book}/purchase', 'BookController@purchase')->name('books.purchase');
	Route::post('books/{book}/buy/deposit', 'BookController@buyDeposit')->name('books.buy.deposit');
	Route::get('books/{book}/users/bought', 'UserListController@boughtBook')->name('books.users.bought');
	Route::get('/unitpay/incoming_payment', 'UnitPayController@incomingPayment')->name('unitpay.incoming_payment');
	Route::get('/unitpay/deposit/success', 'UnitPayController@depositSuccess')->name('unitpay.payment.success');
	Route::get('/unitpay/deposit/error', 'UnitPayController@depositError')->name('unitpay.payment.error');

	Route::get('books_on_moderation', 'BookListController@books_on_moderation')->name('books.on_moderation');
	/*
		Route::get('books_move', 'BookController@moveForm')->name('books.move_form');
		Route::post('books_move', 'BookController@move')->name('books.move');
	*/
	Route::post('/books/{book}/group', 'BookGroupController@group')->name('books.group.attach');
	Route::get('/books/{book}/group/remove', 'BookGroupController@remove')->name('books.group.detach');
	Route::get('/books/{book}/group/make_main_in_group', 'BookGroupController@makeMainInGroup')->name('books.group.make_main_in_group');
	Route::get('books/{book}/tryConvertAgain', 'BookController@tryConvertAgain');
	Route::get('books/{book}/attachments/{id}/setCover/', 'AttachmentController@setCover')->name('books.attachments.set_cover');
	Route::any('books/{book}/attachments/storeFromCkeditor', [
		'uses' => 'AttachmentController@storeFromCkeditor',
		'nocsrf' => true
	])->name('books.attachments.store_from_sceditor');
	Route::get('books/{book}/remove_cover', 'AttachmentController@removeCover')->name('books.remove_cover');
	Route::resource('sequences', 'SequenceController', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
	Route::get('sequences/{sequence}/delete', 'SequenceController@delete')->name('sequences.delete');
	Route::get('sequences/{sequence}/toggle_my_library', 'SequenceController@toggle_my_library')->name('sequences.favorites.toggle');
	Route::get('sequences/{sequence}/read_status/{code}', 'SequenceController@read_status');
	Route::get('sequences/{sequence}/book_numbers', 'SequenceController@book_numbers')->name('sequences.book_numbers');
	Route::post('sequences/{sequence}/book_numbers', 'SequenceController@book_numbers_save')->name('sequences.book_numbers_save');
	Route::get('sequences/{sequence}/merge', 'SequenceController@mergeForm')->name('sequences.merge_form');
	Route::post('sequences/{sequence}/merge', 'SequenceController@merge')->name('sequences.merge');
	Route::get('sequences/{sequence}/unmerge', 'SequenceController@unmerge')->name('sequences.unmerge');
	Route::get('sequences/{sequence}/activity_logs', 'SequenceController@activity_logs')->name('sequences.activity_logs');
	Route::resource('groups', 'UserGroupController');
	Route::get('groups/{group}/destroy', 'UserGroupController@destroy');
	Route::get('/likes/{type}/{id}', 'LikeController@like')->name('likes.store');
	Route::get('/likes/{type}/{id}/users', 'UserListController@whoLikes')->name('likes.users');

	Route::get('users/{user}/bookmarks', 'BookmarkController@index')->name('users.bookmarks.index');
	Route::get('bookmark_folders/list', 'BookmarkFolderController@list')->name('bookmark_folders.list');
	Route::resource('bookmark_folders', 'BookmarkFolderController');
	Route::post('bookmark_folders/save_position', 'BookmarkFolderController@savePosition')->name('users.bookmark_folders.save_position');
	Route::resource('bookmarks', 'BookmarkController');
	Route::get('/users/{user}/bookmark_folders', 'BookmarkFolderController@index')->name('users.bookmark_folders.index');
	Route::get('complaints', 'ComplainController@index')->name('complaints.index');
	Route::get('complaints/{complain}', 'ComplainController@show')->name('complaints.show');
	Route::get('complaints/{type}/{id}/report', 'ComplainController@create_edit')->name('complains.report');
	Route::get('complaints/{type}/{id}/{user}', 'ComplainController@show')->where('user', '[0-9]+');
	Route::post('complaints/{type}/{id}/save', 'ComplainController@save')->name('complains.save');
	Route::get('complaints/{complain}/check', 'ComplainController@check')->name('complains.approve');
	Route::get('complaints/{complain}/start_review', 'ComplainController@startReview')->name('complains.start_review');
	Route::get('complaints/{complain}/stop_review', 'ComplainController@stopReview')->name('complains.stop_review');

	// Admin Notes
	Route::resource('admin_notes', 'AdminNoteController');
	Route::get('admin_notes/{admin_note}/delete', 'AdminNoteController@destroy')->name('admin_notes.delete');

	// файлы книги
	Route::resource('books.files', 'BookFileController', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
	Route::get('/books/{book}/files/{file}/delete', 'BookFileController@destroy');
	Route::get('book_files/on_moderation', 'BookFileController@onModeration')->name('book_files.on_moderation');
	Route::get('book_files/{file}/check', 'BookFileController@check')->name('book_files.approve');
	Route::get('book_files/{file}/decline', 'BookFileController@decline')->name('book_files.decline');
	Route::get('book_files/{file}/set_source_and_make_pages', 'BookFileController@setAsSourceAndMakePages')->name('book_files.set_source_and_make_pages');
	Route::get('book_files/{file}/activity_logs', 'BookFileController@activity_logs')->name('book_files.activity_logs');
	// комментарии
	Route::resource('comments', 'CommentController', ['only' => ['edit', 'update', 'destroy']]);
	Route::get('/{commentable_type}/{commentable_id}/comments/create', 'CommentController@create')->name('comments.create')
		->where('commentable_id', '[0-9]+')->where('commentable_type', '[A-z0-9]+');
	Route::post('/{commentable_type}/{commentable_id}/comments/{parent?}', 'CommentController@store')->name('comments.store')
		->where('commentable_id', '[0-9]+')->where('commentable_type', '[A-z0-9]+');
	Route::get('/comments/{comment}/vote/{vote}', 'CommentController@vote')->where('vote', '(1|-1)')->name('comments.vote');
	Route::get('/comments/{comment}/usersWhoLikesComment', 'UserListController@usersWhoLikesComment')->name('users.comments.who_likes');
	Route::get('/comments/{comment}/usersWhoDislikesComment', 'UserListController@usersWhoDislikesComment')->name('users.comments.who_dislikes');
	Route::get('/comments_on_check', 'CommentController@onCheck')->name('comments.on_check');
	Route::get('/comments/{comment}/approve', 'CommentController@approve')->name('comments.approve');
	Route::delete('/comments/{comment}', 'CommentController@destroy')->name('comments.destroy');
	Route::get('/comments/{comment}/restore', 'CommentController@restore')->name('comments.restore');
	Route::get('/comments/{comment}/publish', 'CommentController@publish')->name('comments.publish');
	// форум
	Route::resource('forum_groups', 'ForumGroupController', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
	Route::post('/forum_groups/change_order', 'ForumGroupController@changeOrder');
	Route::post('/forum_groups/{forum_group}/change_order', 'ForumGroupController@changeForumsOrder');
	Route::resource('forums', 'ForumController', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
	Route::get('/forums/{topic}/delete', 'ForumController@destroy')->name('forums.delete');
	Route::get('/topics/{topic}/posts/move', 'PostController@move');
	Route::get('/topics/{topic}/subscribe', 'TopicController@subscribeToggle')->name('topics.subscribe');
	Route::get('/topics/{topic}/unsubscribe', 'TopicController@subscribeToggle')->name('topics.unsubscribe');
	Route::resource('posts', 'PostController', ['only' => ['edit', 'update', 'destroy']]);
	Route::get('/posts/{post}', 'PostController@show')->name('posts.show');
	Route::get('/topics/{topic}/posts/create', 'PostController@create')->name('posts.create');
	Route::post('/topics/{topic}/posts', 'PostController@store')->name('posts.store');
	Route::get('/forums/search', 'ForumController@search');

	Route::resource('topics', 'TopicController', ['only' => ['edit', 'update', 'destroy']])->middleware('db.transaction');
	Route::get('/forums/{forum}/topics/create', 'TopicController@create')->name('topics.create');
	Route::post('/forums/{forum}/topics', 'TopicController@store')->name('topics.store');
	Route::get('/topics/{topic}/posts/create/{parent?}', 'PostController@create')->where('parent', '[0-9]+');
	Route::post('/topics/{topic}/posts/{parent?}', 'PostController@store')->where('parent', '[0-9]+');
	Route::get('/posts/{post}/fix', 'PostController@fix')->name('posts.fix');
	Route::get('/posts/{post}/unfix', 'PostController@unfix')->name('posts.unfix');
	Route::get('/topics/{topic}/open', 'TopicController@open')->name('topics.open');
	Route::get('/topics/{topic}/close', 'TopicController@close')->name('topics.close');
	Route::get('/topics/{topic}/archive', 'TopicController@archive')->name('topics.archive');
	Route::get('/topics/{topic}/unarchive', 'TopicController@unarchive')->name('topics.unarchive');
	Route::get('/topics/{topic}/merge', 'TopicController@mergeForm')->name('topics.merge_form');
	Route::post('/topics/{topic}/merge', 'TopicController@merge')->name('topics.merge');
	Route::get('/topics/{topic}/move', 'TopicController@moveForm')->name('topics.move_form');
	Route::post('/topics/{topic}/move', 'TopicController@move')->name('topics.move');
	Route::get('/topics/{topic}/label/{label}', 'TopicController@changeLabel')->name('topics.label.change');
	Route::get('/topics/search', 'TopicController@search')->name('topics.search');
	Route::get('/topics/archived', 'TopicController@archived')->name('topics.archived');
	Route::get('/posts/move', 'PostController@move')->name('posts.move');
	Route::post('/posts/move', 'PostController@transfer')->name('posts.transfer');
	Route::get('/posts/search', 'PostController@search');
	Route::get('/posts_on_check', 'PostController@onCheck')->name('posts.on_check');
	Route::get('/posts/{post}/approve', 'PostController@approve')->name('posts.approve');
	Route::resource('users.photos', 'UserPhotoController')->only(['index', 'store']);
	Route::get('/users/{user}/photos/{photo}/delete', 'UserPhotoController@destroy')->name('users.photos.delete');

	Route::get('/users/{user}/avatar', 'UserController@avatar')->name('users.avatar.show');

	Route::get('/settings', 'SettingController@index')->name('settings.index');
	Route::post('/settings', 'SettingController@save')->name('settings.save');
	Route::post('/settings/test_mail', 'SettingController@test_mail')->name('settings.test_mail');
	Route::get('/admin/refresh_counters', 'SettingController@refresh_counters')->name('admin.refresh_counters');
	Route::resource('achievements', 'AchievementController');
	Route::get('achievements_search', 'AchievementController@search')->name('achievements.search');
	Route::get('/users/{user}/achievements', 'UserController@achievements')->name('users.achievements');
	Route::post('/users/{user}/achievements', 'UserController@attach_achievement')->name('users.achievements.attach');
	Route::get('/users/{user}/achievements/{achievement}/detach', 'UserController@detach_achievement')->name('users.achievements.detach');
	Route::resource('users.social_accounts', 'UserSocialAccountController');
	Route::get('/users/{user}/social_accounts/{id}/detach', 'UserSocialAccountController@detach')->name('users.social_accounts.detach');
	Route::resource('keywords', 'KeywordController', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
	Route::get('/users/{user}/notes', 'UserNoteController@index')->name('users.notes.index');
	Route::get('/users/{user}/notes/create', 'UserNoteController@create')->name('users.notes.create');
	Route::post('/users/{user}/notes', 'UserNoteController@store')->name('users.notes.store');
	Route::get('/notes/{note}', 'UserNoteController@show')->name('notes.show');
	Route::get('/notes/{note}/edit', 'UserNoteController@edit')->name('notes.edit');
	Route::patch('/notes/{note}', 'UserNoteController@update')->name('notes.update');
	Route::delete('/notes/{id}', 'UserNoteController@destroy')->name('notes.destroy');
	Route::resource('awards', 'AwardController', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
	Route::post('/books/{book}/awards', 'BookAwardController@store')->name('books.awards.store');
	Route::delete('/books/{book}/awards/{award}', 'BookAwardController@destroy')->name('books.awards.destroy');

	Route::get('/users/{user}/managers', 'UserController@managers')->name('users.managers.index');

	Route::resource('authors.photos', 'AuthorPhotoController', ['only' => ['store', 'destroy']]);
	Route::get('/authors/{author}/photos/{id}/delete', 'AuthorPhotoController@destroy')->name('authors.photos.delete');

	Route::get('/users/{user}/notifications', 'NotificationController@index')->name('users.notifications.index');

	Route::resource('users.blogs', 'BlogController', ['except' => ['show']]);
	Route::get('/users/{user}/blogs/{blog}/fix', 'BlogController@fix')->name('users.blogs.fix');
	Route::get('/users/{user}/blogs/{blog}/unfix', 'BlogController@unfix')->name('users.blogs.unfix');
	Route::get('/wall_posts_on_review', 'BlogController@onReview')->name('wall_posts.on_review');
	Route::get('/blogs/{blog}/approve', 'BlogController@approve')->name('blogs.approve');

	Route::get('/purchase_rules', 'TextBlockController@purchaseRules')->name('purchase_rules');
	Route::get('/sales_rules', 'TextBlockController@salesRules')->name('sales_rules');
	Route::get('/paid_book_publishing_rules', 'TextBlockController@paidBookPublishingRules')->name('paid_book_publishing_rules');

	Route::get('/mailings', 'MailingController@index')->name('mailings.index');
	Route::get('/mailings/create', 'MailingController@create')->name('mailings.create');
	Route::post('/mailings', 'MailingController@store')->name('mailings.store');

	Route::get('/authors/show_parsed_data', 'AuthorController@showParsedData')->name('authors.show_parsed_data');
	Route::get('/authors/get_addresses_for_mailing_to_invite_selling_books', 'AuthorController@getAddressesForMailingToInviteSellingBooks')->name('authors.get_addresses_for_mailing_to_invite_selling_books');

	Route::get('/preview/notification/invitation_to_sell_books', 'OtherController@previewInvitationToSellBooksNotification')
		->name('preview.notification.invitation_to_sell_books');

	Route::get('/frequently_used_styles', 'SettingController@frequentlyUsedStyles')
		->name('frequently_used_styles');

	Route::post('/ideas/store', 'IdeaController@store')->name('ideas.store');
	Route::get('/ideas/search', 'IdeaController@search')->name('ideas.search');

	Route::get('/surveys', 'SurveyController@index')->name('surveys.index');
	Route::get('/surveys/create', 'SurveyController@create')->name('surveys.create');
	Route::post('/surveys', 'SurveyController@store')->name('surveys.store');

	Route::post('/questions', 'TopicController@storeQuestion')->name('questions.store');

	Route::resource('ad_blocks', 'AdBlockController', ['except' => 'show']);
	Route::get('ad_blocks/{ad_block}/delete', 'AdBlockController@destroy')->name('ad_blocks.delete');
	Route::get('ad_blocks/{ad_block}/enable', 'AdBlockController@enable')->name('ad_blocks.enable');
	Route::get('ad_blocks/{ad_block}/disable', 'AdBlockController@disable')->name('ad_blocks.disable');

	Route::get('/preview/notification/user_registered', 'OtherController@previewUserRegisteredNotification')->name('preview.notification.user_registered');
	Route::get('/preview/notification/invoice_was_successfully_paid', 'OtherController@previewInvoiceWasSuccessfullyPaidNotification')->name('preview.notification.invoice_was_successfully_paid');
	Route::get('/preview/invitation_take_survey', 'OtherController@previewInvitationToTakeSurvey')->name('preview.invitation_take_survey');
	Route::get('/preview/welcome_notification', 'OtherController@welcomeNotification')->name('preview.welcome_notification');

	Route::get('/users/{user}/support_questions', 'UserController@supportRequests')->name('users.support_questions.index');
	Route::get('/users/{user}/support_questions/create', 'SupportQuestionController@create')->name('support_questions.create');
	Route::post('/users/{user}/support_questions', 'SupportQuestionController@store')->name('support_questions.store');

	Route::get('/support', 'SupportQuestionController@support')->name('support');
	Route::get('/support_questions/{support_question}', 'SupportQuestionController@show')->name('support_questions.show');
	Route::post('/support_question_messages', 'SupportQuestionMessageController@store')->name('support_question_messages.store')->middleware('db.transaction');
	Route::get('/support_questions/{support_question}/start_review', 'SupportQuestionController@startReview')->name('support_questions.start_review');
	Route::get('/support_questions/{support_question}/approve', 'SupportQuestionController@approve')->name('support_questions.approve');
	Route::get('/support_questions/{support_question}/stop_review', 'SupportQuestionController@stopReview')->name('support_questions.stop_review');
	Route::get('/support_questions/{support_question}/solve', 'SupportQuestionController@solve')->name('support_questions.solve');
	Route::get('/support_questions', 'SupportQuestionController@index')->name('support_questions.index');
	Route::get('/support_questions/solved', 'SupportQuestionController@solved')->name('support_questions.solved');
	Route::get('/support_questions/in_process_of_solving', 'SupportQuestionController@inProcessOfSolving')->name('support_questions.in_process_of_solving');
	Route::get('/support_questions/unsolved', 'SupportQuestionController@unsolved')->name('support_questions.unsolved');
    Route::get('/support_questions/{support_question}/edit', 'SupportQuestionController@edit')->name('support_questions.edit');
    Route::patch('/support_questions/{support_question}', 'SupportQuestionController@update')->name('support_questions.update');

	Route::get('/support_questions/{support_question}/feedbacks/create', 'SupportQuestionController@feedbackCreate')->name('support_questions.feedbacks.create');
	Route::post('/support_questions/{support_question}/feedbacks', 'SupportQuestionController@feedbackStore')->name('support_questions.feedbacks.store');
});

Route::get('/books/{book}/pages', 'SectionController@page')->name('books.pages');

Route::get('/surveys/guest/create', 'SurveyController@createGuest')->name('surveys.guest.create')->middleware('signed');
Route::post('/surveys/guest', 'SurveyController@storeGuest')->name('surveys.guest.store')->middleware('signed');

Route::get('/ideas', 'IdeaController@index')->name('ideas.index');
Route::get('/ideas/card/hide', 'IdeaController@hideCard')->name('ideas.card.hide');

Route::resource('collections', 'CollectionController', ['only' => ['index', 'show']]);

Route::get('/personal_data_processing_agreement', 'TextBlockController@personalDataProcessingAgreement')->name('personal_data_processing_agreement');
Route::get('/users_refer', 'OtherController@refer')->name('users.refer');

Route::get('/books/{book}/files/{fileName}', 'BookFileController@show')->name('books.files.show');

Route::resource('keywords', 'KeywordController', ['only' => ['index']]);
Route::get('users/{user}/settings/email_delivery/without_authorization', 'UserSettingController@emailDeliveryWithoutAuthorization')->name('users.settings.email_delivery.edit.without_authorization')->middleware('signed');
Route::patch('users/{user}/settings/email_delivery/without_authorization', 'UserSettingController@emailDeliveryUpdateWithoutAuthorization')->name('users.settings.email_delivery.update.without_authorization')->middleware('signed');
Route::get('/user_pass_age_restriction', 'OtherController@userPassAgeRestriction')->name('user_pass_age_restriction');

Route::resource('authors.photos', 'AuthorPhotoController', ['only' => ['index', 'show']]);
Route::resource('sequences', 'SequenceController', ['only' => ['index', 'show']]);
Route::resource('awards', 'AwardController', ['only' => ['index', 'show']]);
Route::resource('forum_groups', 'ForumGroupController', ['only' => ['index', 'show']]);
Route::resource('forums', 'ForumController', ['only' => ['index', 'show']]);
Route::get('/posts/{post}/descendants', 'PostController@descendants')->name('posts.descendants');
Route::get('/forums/{forum}/posts', 'PostListController@forum');
Route::resource('topics.posts', 'PostController', ['only' => ['index', 'show']]);
Route::get('/topics/{topic}/posts', 'PostListController@topic')->name('topics.posts.index');
Route::get('/posts/{post}/go_to', 'PostController@go_To')->name('posts.go_to');
//Route::resource('topics', 'TopicController');
Route::get('/topics', 'TopicListController@index')->name('topics.index');
Route::resource('topics', 'TopicController', ['only' => ['show']]);

Route::get('/auth/{provider}', 'UserSocialAccountController@redirectToProvider')
	->name('social_accounts.redirect')
	->where('provider', '(google|facebook|vkontakte)');
Route::get('/auth/{provider}/callback', 'UserSocialAccountController@handleProviderCallback')
	->name('social_accounts.callback')
	->where('provider', '(google|facebook|vkontakte)');

Route::get('/books/{book}/awards', 'BookAwardController@index')->name('books.awards.index');
Route::resource('books.sections', 'SectionController', ['only' => ['index', 'show']]);
Route::resource('books.notes', 'NoteController', ['only' => ['index', 'show']]);
Route::get('/posts', 'PostListController@index');

// middleware guest end
Route::get('/users_search', 'UserController@search');
Route::get('/users/{user}/blogs/{blog}/goTo', 'BlogController@go_To')->name('users.blogs.go');
Route::get('/users/{user}/blogs/{blog}/loadChild', 'BlogController@descendants')->name('users.blogs.descendants');
Route::get('/blogs/{blog}', 'BlogController@show')->name('blogs.show');
Route::get('/comments/{comment}/go_to', 'CommentController@go_To')->name('comments.go');
Route::get('/comments/{comment}/descendants', 'CommentController@descendants');

Route::get('authors', 'AuthorListController@index')->name('authors');

Route::get('/authors/{author}/authors', 'AuthorController@authors')->name('authors.group.index');
Route::post('/authors/{author}/group', 'AuthorController@group')->name('authors.group');
Route::get('/authors/{author}/ungroup', 'AuthorController@ungroup')->name('authors.ungroup');
Route::get('/authors/{author}/comments', 'AuthorController@comments')->name('authors.comments');
Route::get('/authors/{author}/books/', 'AuthorController@books')->name('authors.books');
Route::get('/authors/{author}/translated_books/', 'AuthorController@show')->name('authors.translated_books');
Route::get('/authors/{author}/forum/', 'AuthorController@forum')->name('authors.forum');
Route::get('/authors/{author}/books_votes/', 'AuthorController@books_votes')->name('authors.books_votes');
Route::get('sequences', 'SequenceListController@index')->name('sequences');
Route::get('/sequences/{sequence}/books/', 'SequenceController@books')->name('sequences.books');
Route::get('/sequences/{sequence}/comments/', 'SequenceController@comments')->name('sequences.comments');
Route::any('/sequences/search', 'SequenceController@search')->name('sequences.search');

Route::get('/users/{user}/group/{group}', 'UserController@group');
Route::get('/welcome', 'TextBlockController@welcome')->name('welcome');

Route::get('/text_blocks/{name}/create', 'TextBlockController@create')->name('text_blocks.create');
Route::post('/text_blocks/{name}', 'TextBlockController@store')->name('text_blocks.store');
Route::get('/text_blocks/{name}/edit', 'TextBlockController@edit')->name('text_blocks.edit');
Route::patch('/text_blocks/{name}', 'TextBlockController@update')->name('text_blocks.update');
Route::get('/text_blocks/{name}/versions/{id}', 'TextBlockController@show')->name('text_blocks.show');
Route::get('/text_blocks/{name}/versions', 'TextBlockController@versions')->name('text_blocks.versions.index');
Route::get('/text_blocks/{name}/latest', 'TextBlockController@showLatestVersionForName')->name('text_blocks.show_lastest_version_for_name');

Route::any('genres/loadList', 'GenreController@search');
Route::any('spellchecker', 'OtherController@spellchecker');
Route::get('/keywords_helper', 'TextBlockController@keywordsHelper')->name('text_block.keywords_helper');
Route::get('user_agents/{model}/{id}/', 'UserAgentController@show')->name('user_agents.show');
Route::get('/likes/{type}/{id}/tooltip', 'LikeController@tooltip')->name('likes.tooltip');

Route::get('/away', 'OtherController@away')->name('away');
Route::get('/mbs', 'OtherController@mediaBreakpointShow');
Route::get('/phpinfo', 'OtherController@phpinfo');
Route::get('/unitpay/handler', 'UnitPayController@handler')->name('unitpay.handler');

Route::get('/preview/notification', 'OtherController@previewNotification')->name('preview.notification');
Route::get('/preview/book_styles', 'OtherController@previewBookStyles')->name('preview.book_styles');
Route::get('/preview/book_styles_for_epub', 'OtherController@previewBookStylesForEpubBooks')->name('preview.book_styles_for_epub');
Route::get('/preview/comment', 'OtherController@previewComment')->name('preview.comment');
Route::get('/preview/sceditor', 'OtherController@previewSceditor')->name('preview.sceditor');
Route::get('/preview/error/500', 'OtherController@previewError500')->name('preview.error.500');

Route::get('/examples/tables/v1', 'OtherController@exampleTable1');
Route::get('/examples/tables/v2', 'OtherController@exampleTable2');
Route::get('/examples/tables/v3', 'OtherController@exampleTable3');
Route::get('/examples/to_bottom/v1', 'OtherController@toBottomV1');
Route::get('/examples/to_bottom/v2', 'OtherController@toBottomV2');
Route::get('/examples/to_bottom/v3', 'OtherController@toBottomV3');
Route::get('/examples/to_bottom/v4', 'OtherController@toBottomV4');

Route::get('/l/{key}', 'UrlShortController@redirect')->name('url.shortener');
Route::get('/sitemap.xml', 'OtherController@sitemapRedirect')->name('sitemap');

Route::fallback('OtherController@routeFallback');

Route::get('/ad_view_test', 'OtherController@adViewTest')->name('ad_view_test');

Route::get('bs', 'OldRoutesController@show');
Route::get('br', 'OldRoutesController@show');
Route::get('bd', 'OldRoutesController@show');
Route::get('a', 'OldRoutesController@show');
Route::get('series', 'OldRoutesController@show');
Route::get('books_in_series', 'OldRoutesController@show');
Route::get('Users', 'OldRoutesController@show');
Route::get('p', 'OldRoutesController@show');
Route::get('ForumRedirectToPost', 'OldRoutesController@show');
Route::get('Topic', 'OldRoutesController@show');
Route::get('BookAddV2', 'OldRoutesController@show');
Route::get('UserBookRate', 'OldRoutesController@show');
Route::get('edit_profile', 'OldRoutesController@show');
Route::get('UserComments', 'OldRoutesController@show');
Route::get('UserLibBook', 'OldRoutesController@show');
Route::get('UserLibAuthor', 'OldRoutesController@show');
Route::get('Forum', 'OldRoutesController@show');
Route::get('as', 'OldRoutesController@show');
Route::get('all_genre', 'OldRoutesController@show');
Route::get('BookRateShow', 'OldRoutesController@show');
Route::get('ShowTalk', 'OldRoutesController@show');
Route::get('UserGenreBlackList', 'OldRoutesController@show');
Route::get('MessageInbox', 'OldRoutesController@show');
Route::get('main_page', 'OldRoutesController@show');
Route::get('UserBookWithStatus', 'OldRoutesController@show');
Route::get('AuthorPageTabLoad', 'OldRoutesController@show');
Route::get('UserBooks', 'OldRoutesController@show');
Route::get('UserPosts', 'OldRoutesController@show');
Route::get('UserLibSequence', 'OldRoutesController@show');
Route::get('UserPosts', 'OldRoutesController@show');
Route::get('AllKeywords', 'OldRoutesController@show');
Route::get('ForumPostSearch', 'OldRoutesController@show');
Route::get('ShowUsersVoteForBook', 'OldRoutesController@show');
Route::get('most_viewed_books', 'OldRoutesController@show');
Route::get('add_book_fb2', 'OldRoutesController@show');
Route::get('BookRatingPeriod', 'OldRoutesController@show');
Route::get('Forums', 'OldRoutesController@show');
Route::get('UsersOnModerate', 'OldRoutesController@show');
Route::get('AuthorComments', 'OldRoutesController@show');
Route::get('AuthorRateShow', 'OldRoutesController@show');
Route::get('Comments', 'OldRoutesController@show');
Route::get('BlogRecordRedirectTo', 'OldRoutesController@show');
Route::get('CommentRedirectTo', 'OldRoutesController@show');
Route::get('Rules', 'OldRoutesController@show');
Route::get('ForRightsOwners', 'OldRoutesController@show');
Route::get('WhoLike', 'OldRoutesController@show');
Route::get('show_users', 'OldRoutesController@show');
Route::get('AllBooks', 'OldRoutesController@show');
Route::get('UserPosts', 'OldRoutesController@show');