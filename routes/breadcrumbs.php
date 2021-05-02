<?php


use App\BookGroup;
use App\Collection;

Breadcrumbs::for('welcome', function ($breadcrumbs) {
	$breadcrumbs->push(__('common.welcome'), route('welcome'));
});

Breadcrumbs::for('home', function ($breadcrumbs) {
	$breadcrumbs->push(__('common.main'), route('home'));
});

Breadcrumbs::for('genres', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(trans_choice('genre.genres', 2), route('genres'));
});

Breadcrumbs::for('genres.create', function ($breadcrumbs) {
	$breadcrumbs->parent('genres');
	$breadcrumbs->push(__('genre.create'), route('genres.create'));
});

Breadcrumbs::for('genres.show', function ($breadcrumbs, $genre) {

	//$genre = App\Genre::whereIdWithSlug($genre)->first();

	if ($genre instanceof \App\Genre) {

		$breadcrumbs->parent('genres');

		if ($genre->isMain()) {
			$breadcrumbs->push($genre->name, route('genres.show', ['genre' => $genre->getIdWithSlug()]));
		} else {
			if ($genre->group) {
				$breadcrumbs->push($genre->group->name, route('genres.show', ['genre' => $genre->group->getIdWithSlug()]));
			}

			$breadcrumbs->push($genre->name, route('genres.show', ['genre' => $genre->getIdWithSlug()]));
		}

		$breadcrumbs->parent('books');
	}
});

Breadcrumbs::for('genres.edit', function ($breadcrumbs, $genre) {
	$breadcrumbs->parent('genres.show', $genre);
	$breadcrumbs->push(__('common.edit'), route('genres.edit', $genre));
});

Breadcrumbs::for('authors', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(trans_choice('author.authors', 2), route('authors'));
});

Breadcrumbs::for('books', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(trans_choice('book.books', 2), route('books'));

	if (!empty($kws = request('kw'))) {
		$kws = (array)$kws;

		foreach ($kws as $kw) {
			if (!empty($kw))
				$breadcrumbs->push($kw, route('books', ['kw' => $kw]));
		}

	} elseif (request('si') == 'only') {
		$breadcrumbs->push(__('book.is_si'), route('books', ['si' => 'only']));
	} elseif (request('Formats')) {
		$formats = (array)request('Formats');

		foreach ($formats as $format)
			$breadcrumbs->push($format, route('books', ['Formats' => $format]));
	} elseif (request('rs') == 'complete') {
		$breadcrumbs->push(__('book.complete_books'), route('books', ['rs' => 'complete']));
	} elseif (request('read_access') == 'open') {
		$breadcrumbs->push(__('book.read_access_open'), route('books', ['read_access' => 'open']));
	} elseif (request('download_access') == 'open') {
		$breadcrumbs->push(__('book.download_access_open'), route('books', ['download_access' => 'open']));
	}
	if (!empty($award = request('award'))) {
		$award = App\Award::where('title', $award)->first();

		if (!empty($award))
			$breadcrumbs->push($award->title, route('books', ['award' => $award->title]));
	}
});

Breadcrumbs::for('awards.index', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(trans_choice('award.awards', 2), route('awards.index'));
});

Breadcrumbs::for('sequences', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(trans_choice('sequence.sequences', 2), route('sequences'));
});

Breadcrumbs::for('sequences.create', function ($breadcrumbs) {
	$breadcrumbs->parent('sequences');
	$breadcrumbs->push(__('common.create'), route('sequences.create'));
});

Breadcrumbs::for('forums.index', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(trans_choice('forum.forums', 2), route('forums.index'));
});

Breadcrumbs::for('forums.show', function ($breadcrumbs, $forum) {
	$breadcrumbs->parent('forums.index');

	if (is_object($forum))
		$breadcrumbs->push($forum->name, route('forums.show', $forum));
});

Breadcrumbs::for('forums.create', function ($breadcrumbs) {
	$breadcrumbs->parent('forums.index');
	$breadcrumbs->push(__('forum.çreate'), route('forums.create'));
});

Breadcrumbs::for('topics.index', function ($breadcrumbs, $forum = null) {
	if (!empty($forum)) {
		$breadcrumbs->parent('forums.index');

		if (is_object($forum))
			$breadcrumbs->push($forum->name, route('topics.index', $forum));
	}
});

Breadcrumbs::for('topics.create', function ($breadcrumbs, $forum) {
	$breadcrumbs->parent('forums.index');

	if (is_object($forum))
		$breadcrumbs->push($forum->name, route('forums.show', $forum));
});

Breadcrumbs::for('topics.show', function ($breadcrumbs, $topic) {

	if ($topic instanceof \App\Topic)
		$forumable = optional($topic->forum)->forumable;

	if (isset($forumable) and $forumable instanceof \App\Author) {
		$breadcrumbs->parent('authors.forum', $forumable);
	} else {
		$breadcrumbs->parent('forums.index');

		if (is_object($topic) and !empty($topic->forum))
			$breadcrumbs->push($topic->forum->name, route('forums.show', $topic->forum));
	}

	if ($topic instanceof \App\Topic) {

		if (!empty($topic) and isset($topic->name))
			$breadcrumbs->push($topic->name, route('topics.show', ['topic' => $topic]));
	}
});

Breadcrumbs::for('topics.edit', function ($breadcrumbs, $topic) {

	$breadcrumbs->parent('forums.index');

	if (!empty($topic->forum))
		$breadcrumbs->push($topic->forum->name, route('forums.show', $topic->forum));

	if (is_object($topic))
		$breadcrumbs->push($topic->name, route('topics.show', ['topic' => $topic]));

	$breadcrumbs->push(__('common.edit'), route('topics.edit', ['topic' => $topic]));
});

Breadcrumbs::for('forums.edit', function ($breadcrumbs, $forum) {
	$breadcrumbs->parent('forums.show', $forum);
	$breadcrumbs->push(__('common.edit'), route('forums.edit', $forum));
});

Breadcrumbs::for('posts.edit', function ($breadcrumbs, $post) {

	if (!empty($post->topic))
		$breadcrumbs->parent('topics.show', $post->topic);

	$breadcrumbs->push('Сообщение', route('posts.go_to', $post));
	$breadcrumbs->push(__('common.edit'), route('posts.edit', ['post' => $post]));
});

Breadcrumbs::for('topics.merge_form', function ($breadcrumbs, $topic) {
	$breadcrumbs->parent('topics.show', $topic);
	$breadcrumbs->push(__('topic.merge'), route('topics.merge_form', $topic));
});

Breadcrumbs::for('topics.move_form', function ($breadcrumbs, $topic) {
	$breadcrumbs->parent('topics.show', $topic);
	$breadcrumbs->push(__('topic.move'), route('topics.move_form', $topic));
});

Breadcrumbs::for('posts.move', function ($breadcrumbs) {
	$breadcrumbs->parent('forums.index');
	$breadcrumbs->push(trans_choice('post.move', 2), route('posts.move'));
});

Breadcrumbs::for('books.show', function ($breadcrumbs, &$book) {
	if (is_object($book) and !$book->trashed() and $book->isHaveAccess()) {

		if (!empty($author = $book->writers->first()))
			$breadcrumbs->push($author->fullName, route('authors.show', ['author' => $author]));

		$breadcrumbs->push(trans_choice('book.books', 1) . ' "' . $book->title . '"', route('books.show', $book));
	}
});

Breadcrumbs::for('books.edit', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('common.edit'), route('books.edit', $book));
});

Breadcrumbs::for('books.access', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('book.access'), route('books.access', $book));
});

Breadcrumbs::for('books.add_to_private.form', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('book.add_to_private'));
});

Breadcrumbs::for('books.editions.edit', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('book.edit_group'), route('books.editions.edit', $book));
});

Breadcrumbs::for('books.group.index', function ($breadcrumbs, $group) {

	if (!is_object($group))
		$group = BookGroup::find(intval($group));

	if (!empty($group)) {
		if (!empty($group->main_book)) {
			$breadcrumbs->parent('books.show', $group->main_book);
			$breadcrumbs->push(__('book.in_group'), route('books.group.index', $group->main_book));
		}
	}
});

Breadcrumbs::for('books.awards.index', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(trans_choice('award.awards', 2), route('books.awards.index', $book));
});

Breadcrumbs::for('books.keywords.index', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(trans_choice('book_keyword.book_keywords', 2), route('books.keywords.index', $book));
});

Breadcrumbs::for('books.old.page', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.sections.index', $book);
	$breadcrumbs->push(__("common.read"), route('books.old.page', ['book' => $book]));
});

Breadcrumbs::for('books.sections.index', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__("section.contents"), route('books.sections.index', $book));
});

Breadcrumbs::for('books.sections.create', function ($breadcrumbs, $book) {

	if (is_object($book)) {
		$breadcrumbs->parent('books.sections.index', $book);

		if (!empty(request()->parent)) {
			$parent_section = App\Section::where('book_id', $book->id)
				->findInnerIdOrFail(request()->parent);

			$ancestors = $parent_section->ancestors;

			foreach ($ancestors as $ancestor) {
				$breadcrumbs->push($ancestor->title, route('books.sections.show', ['book' => $book, 'section' => $ancestor->inner_id]));
			}

			$breadcrumbs->push($parent_section->title, route('books.sections.show', ['book' => $book, 'section' => $parent_section->inner_id]));

			$breadcrumbs->push(__('section.create_subsection'), route('books.sections.create', $book));
		} else {
			$breadcrumbs->push(__('common.create'), route('books.sections.create', $book));
		}
	}
});

Breadcrumbs::for('books.sections.show', function ($breadcrumbs, $book, $section) {

	if (is_object($book) and is_object($section)) {
		if ($section->isSection())
			$breadcrumbs->parent('books.sections.index', $book);
		if ($section->isNote())
			$breadcrumbs->parent('books.notes.index', $book);
	}

	if (is_object($section)) {
		$ancestors = $section->ancestors;

		foreach ($ancestors as $ancestor) {
			$breadcrumbs->push($ancestor->title, route('books.sections.show', ['book' => $book, 'section' => $ancestor->inner_id]));
		}

		$breadcrumbs->push($section->title, route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]));

		$breadcrumbs->push(__("common.read"), route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]));
	}
});

Breadcrumbs::for('books.sections.edit', function ($breadcrumbs, $book, $section) {

	if (is_object($book)) {
		if ($section->isSection())
			$breadcrumbs->parent('books.sections.index', $book);
		if ($section->isNote())
			$breadcrumbs->parent('books.notes.index', $book);
	}

	if (is_object($section)) {
		$ancestors = $section->ancestors;

		foreach ($ancestors as $ancestor) {
			$breadcrumbs->push($ancestor->title, route('books.sections.show', ['book' => $book, 'section' => $ancestor->inner_id]));
		}

		$breadcrumbs->push($section->title, route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]));
		$breadcrumbs->push(__('common.edit'), route('books.sections.edit', ['book' => $book, 'section' => $section->inner_id]));
	}
});

Breadcrumbs::for('books.cover.show', function ($breadcrumbs, $book) {

	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('Cover'), route('books.cover.show', ['book' => $book]));
});

Breadcrumbs::for('books.notes.index', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(trans_choice('note.notes', 2), route('books.notes.index', $book));
});

Breadcrumbs::for('books.notes.create', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.notes.index', $book);
	$breadcrumbs->push(__('common.create'), route('books.sections.create', $book));
});

Breadcrumbs::for('books.notes.show', function ($breadcrumbs, $book, $note) {

	if (!is_object($book))
		$book = \App\Book::findOrFail($book);

	if (!is_object($note))
		$note = $book->sections()->find($note);

	if ($note instanceof \App\Section) {

		if ($note->isNote())
			$breadcrumbs->parent('books.notes.index', $book);

		if (is_object($note)) {
			$breadcrumbs->push($note->title, route('books.notes.show', ['book' => $book, 'note' => $note->inner_id]));
		}
	}
});

Breadcrumbs::for('books.notes.edit', function ($breadcrumbs, $book, $note) {

	if (!is_object($book))
		$book = \App\Book::findOrFail($book);

	if (!is_object($note))
		$note = $book->sections()->findInnerIdOrFail($note);

	if ($note->isNote())
		$breadcrumbs->parent('books.notes.index', $book);

	if (is_object($note)) {
		$breadcrumbs->push($note->title, route('books.notes.show', ['book' => $book, 'note' => $note->inner_id]));
		$breadcrumbs->push(__('common.edit'), route('books.notes.edit', ['book' => $book, 'note' => $note->inner_id]));
	}
});

Breadcrumbs::for('books.attachments.index', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(trans_choice("attachment.attachments", 2), route('books.attachments.index', $book));
});

Breadcrumbs::for('books.votes', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(trans_choice('book_vote.book_votes', 2), route('books.votes', $book));
});

Breadcrumbs::for('books.readed', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(trans_choice('user.read_status_array.readed', 2), route('books.readed', $book));
});

Breadcrumbs::for('books.read_later', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(trans_choice('user.read_status_array.read_later', 2), route('books.read_later', $book));
});

Breadcrumbs::for('books.read_now', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(trans_choice('user.read_status_array.read_now', 2), route('books.read_now', $book));
});

Breadcrumbs::for('books.read_not_complete', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(trans_choice('user.read_status_array.read_not_complete', 2), route('books.read_not_complete', $book));
});

Breadcrumbs::for('sequences.show', function ($breadcrumbs, $sequence) {

	if (is_object($sequence)) {
		$breadcrumbs->parent('sequences');
		$breadcrumbs->push($sequence->name, route('sequences.show', $sequence));
	}
});

Breadcrumbs::for('sequences.edit', function ($breadcrumbs, $sequence) {
	$breadcrumbs->parent('sequences.show', $sequence);
	$breadcrumbs->push(__('common.edit'), route('sequences.edit', $sequence));
});

Breadcrumbs::for('sequences.books', function ($breadcrumbs, $sequence) {
	$breadcrumbs->parent('sequences.show', $sequence);
	$breadcrumbs->push(trans_choice('', 2) . 'Книги', route('sequences.books', $sequence));
});

Breadcrumbs::for('sequences.comments', function ($breadcrumbs, $sequence) {
	$breadcrumbs->parent('sequences.show', $sequence);
	$breadcrumbs->push(trans_choice('comment.comments', 2), route('sequences.comments', $sequence));
});

Breadcrumbs::for('sequences.book_numbers', function ($breadcrumbs, $sequence) {
	$breadcrumbs->parent('sequences.show', $sequence);
	$breadcrumbs->push('Редактировать номера книг в серии', route('sequences.book_numbers', $sequence));
});

Breadcrumbs::for('users', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(trans_choice('user.users', 2), route('users'));
});

Breadcrumbs::for('profile', function ($breadcrumbs, $user) {
	if (is_object($user) and !$user->trashed()) {
		$breadcrumbs->push($user->userName, route('profile', $user));
	}
});

Breadcrumbs::for('users.avatar.show', function ($breadcrumbs, $user) {

	if (is_object($user)) {
		$breadcrumbs->parent('profile', $user);
		$breadcrumbs->push(__('Photo'), route('users.avatar.show', ['user' => $user]));
	}
});

Breadcrumbs::for('users.edit', function ($breadcrumbs, $user) {

	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('common.edit'), route('users.edit', $user));
});

Breadcrumbs::for('users.friends', function ($breadcrumbs, $user) {

	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('common.friends'), route('users.friends', $user));
});

Breadcrumbs::for('users.subscriptions', function ($breadcrumbs, $user) {

	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('common.subscriptions'), route('users.subscriptions', $user));
});

Breadcrumbs::for('users.subscribers', function ($breadcrumbs, $user) {

	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('common.subscribers'), route('users.subscriptions', $user));
});

Breadcrumbs::for('users.blogs.edit', function ($breadcrumbs, $user, $blog) {

	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('common.edit'), route('users.blogs.edit', ['user' => $user, 'blog' => $blog]));
});

Breadcrumbs::for('users.blogs.create', function ($breadcrumbs, $user) {

	$breadcrumbs->parent('profile', $user);

	$parent = App\Blog::find(request('parent'));

	if (!empty($parent)) {
		$breadcrumbs->push(__('common.reply'));
	}
});

Breadcrumbs::for('users.groups.edit', function ($breadcrumbs, $user) {

	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('user.change_user_group'), route('users.groups.edit', ['user' => $user]));
});

Breadcrumbs::for('users.books.readed', function ($breadcrumbs, $user) {

	if (is_object($user)) {
		$breadcrumbs->parent('profile', $user);
		$breadcrumbs->push(trans_choice('book.books', 2), route('books'));
		$breadcrumbs->push(trans_choice($user->itsMe() ? 'user.my_read_status_array.readed' : 'user.read_status_with_gender_array.readed', $user->gender), route('users.books.readed', ['user' => $user]));
	}
});

Breadcrumbs::for('users.books.read_later', function ($breadcrumbs, $user) {
	if (is_object($user)) {
		$breadcrumbs->parent('profile', $user);
		$breadcrumbs->push(trans_choice('book.books', 2), route('books'));
		$breadcrumbs->push(trans_choice($user->itsMe() ? 'user.my_read_status_array.read_later' : 'user.read_status_with_gender_array.read_later', $user->gender), route('users.books.read_later', ['user' => $user]));
	}
});

Breadcrumbs::for('users.books.read_now', function ($breadcrumbs, $user) {
	if (is_object($user)) {
		$breadcrumbs->parent('profile', $user);
		$breadcrumbs->push(trans_choice('book.books', 2), route('books'));
		$breadcrumbs->push(trans_choice($user->itsMe() ? 'user.my_read_status_array.read_now' : 'user.read_status_with_gender_array.read_now', $user->gender), route('users.books.read_now', ['user' => $user]));
	}
});

Breadcrumbs::for('users.books.read_not_complete', function ($breadcrumbs, $user) {
	if (is_object($user)) {
		$breadcrumbs->parent('profile', $user);
		$breadcrumbs->push(trans_choice('book.books', 2), route('books'));
		$breadcrumbs->push(trans_choice($user->itsMe() ? 'user.my_read_status_array.read_not_complete' : 'user.read_status_with_gender_array.read_not_complete', $user->gender), route('users.books.read_not_complete', ['user' => $user]));
	}
});

Breadcrumbs::for('users.books.not_read', function ($breadcrumbs, $user) {
	if (is_object($user)) {
		$breadcrumbs->parent('profile', $user);
		$breadcrumbs->push(trans_choice('book.books', 2), route('books'));
		$breadcrumbs->push(trans_choice($user->itsMe() ? 'user.my_read_status_array.not_read' : 'user.read_status_with_gender_array.not_read', $user->gender), route('users.books.not_read', ['user' => $user]));
	}
});

Breadcrumbs::for('users.books.comments', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(trans_choice('comment.comments', 2), route('users.books.comments', ['user' => $user]));
});

Breadcrumbs::for('users.posts', function ($breadcrumbs, $user) {

	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(trans_choice('post.posts', 2), route('users.posts', ['user' => $user]));
});

Breadcrumbs::for('users.topics', function ($breadcrumbs, $user) {

	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(trans_choice('topic.topics', 2), route('users.topics', ['user' => $user]));
});

Breadcrumbs::for('users.votes', function ($breadcrumbs, $user) {

	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(trans_choice('user.book_votes', 2), route('users.votes', ['user' => $user]));
});

Breadcrumbs::for('users.books', function ($breadcrumbs, $user) {

	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(trans_choice('book.books', 2), route('books'));
	$breadcrumbs->push(__('common.favorites'), route('users.books', ['user' => $user]));
});

Breadcrumbs::for('users.authors', function ($breadcrumbs, $user) {

	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(trans_choice('author.authors', 2), route('authors'));
	$breadcrumbs->push(__('common.favorites'), route('users.authors', ['user' => $user]));
});

Breadcrumbs::for('users.sequences', function ($breadcrumbs, $user) {

	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(trans_choice('sequence.sequences', 2), route('sequences'));
	$breadcrumbs->push(__('common.favorites'), route('users.sequences', ['user' => $user]));
});

Breadcrumbs::for('users.comments.who_likes', function ($breadcrumbs, $comment) {

	$breadcrumbs->parent('users');
	$breadcrumbs->push(__('comment.who_likes'), route('users.comments.who_likes', $comment));
});

Breadcrumbs::for('users.comments.who_dislikes', function ($breadcrumbs, $comment) {

	$breadcrumbs->parent('users');
	$breadcrumbs->push(__('comment.who_dislikes'), route('users.comments.who_dislikes', $comment));
});

Breadcrumbs::for('authors.show', function ($breadcrumbs, $author) {

	if (is_object($author) and !$author->trashed() and $author->isHaveAccess()) {
		$breadcrumbs->parent('authors', $author);
		$breadcrumbs->push($author->fullName, route('authors.show', ['author' => $author]));
	}
});

Breadcrumbs::for('authors.photo', function ($breadcrumbs, $author) {

	if (is_object($author)) {
		$breadcrumbs->parent('authors.show', $author);
		$breadcrumbs->push(__('Photo'), route('authors.photo', ['author' => $author]));
	}
});

Breadcrumbs::for('authors.edit', function ($breadcrumbs, $author) {

	$breadcrumbs->parent('authors.show', $author);
	$breadcrumbs->push(__('common.edit'), route('authors.edit', ['author' => $author]));
});

Breadcrumbs::for('authors.managers', function ($breadcrumbs, $author) {

	$breadcrumbs->parent('authors.show', $author);
	$breadcrumbs->push(trans_choice('author.managers', 2), route('authors.managers', ['author' => $author]));
});

Breadcrumbs::for('authors.books', function ($breadcrumbs, $author) {

	$breadcrumbs->parent('authors.show', $author);
	$breadcrumbs->push(trans_choice('book.books', 2), route('authors.books', ['author' => $author]));
});

Breadcrumbs::for('authors.comments', function ($breadcrumbs, $author) {

	$breadcrumbs->parent('authors.show', $author);
	$breadcrumbs->push(trans_choice('comment.comments', 2), route('authors.comments', ['author' => $author]));
});

Breadcrumbs::for('authors.translated_books', function ($breadcrumbs, $author) {

	$breadcrumbs->parent('authors.show', $author);
	$breadcrumbs->push(trans_choice('author.translated_books', 2), route('authors.translated_books', ['author' => $author]));
});

Breadcrumbs::for('authors.forum', function ($breadcrumbs, $author) {

	$breadcrumbs->parent('authors.show', $author);
	$breadcrumbs->push(__('author.forum'), route('authors.forum', ['author' => $author]));
});

Breadcrumbs::for('authors.books_votes', function ($breadcrumbs, $author) {

	$breadcrumbs->parent('authors.show', $author);
	$breadcrumbs->push(trans_choice('book_vote.book_votes', 2), route('authors.books_votes', ['author' => $author]));
});

Breadcrumbs::for('bookmark_folders.index', function ($breadcrumbs) {

	//$breadcrumbs->parent('home');
	$breadcrumbs->push(trans_choice('bookmark.bookmarks', 2), route('bookmark_folders.index'));
});

Breadcrumbs::for('news', function ($breadcrumbs) {

	//$breadcrumbs->parent('home');
	$breadcrumbs->push(__('navbar.news'), route('news'));
});

Breadcrumbs::for('books.create', function ($breadcrumbs) {

	$breadcrumbs->parent('books');
	$breadcrumbs->push(__('common.create'), route('books.create'));
});

Breadcrumbs::for('books.create.description', function ($breadcrumbs, $book) {

	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('book.filling_in_the_description'), route('books.create.description', $book));
});

Breadcrumbs::for('books.create.complete', function ($breadcrumbs, $book) {

	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('book.completion_of_the_addition'), route('books.create.complete', $book));
});

Breadcrumbs::for('book_files.on_moderation', function ($breadcrumbs) {

	//$breadcrumbs->parent('home');
	$breadcrumbs->push(__('book_file.on_check'), route('book_files.on_moderation'));
});

Breadcrumbs::for('users.inbox', function ($breadcrumbs) {

	//$breadcrumbs->parent('home');
	if (auth()->check())
		$breadcrumbs->push(__('navbar.messages'), route('users.inbox', ['user' => auth()->user()]));
});

Breadcrumbs::for('users.messages.index', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('users.inbox');

	if (is_object($user)) {
		$breadcrumbs->push($user->userName, route('profile', $user));
		$breadcrumbs->push(__('common.dialog'), route('users.messages.index', ['user' => $user]));
	}
});

Breadcrumbs::for('messages.edit', function ($breadcrumbs, $message) {
	if (is_object($message)) {
		$breadcrumbs->parent('users.messages.index', $message->recepient);
	}
});

Breadcrumbs::for('managers.on_check', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(trans_choice('manager.on_check', 2), route('managers.on_check'));
});

Breadcrumbs::for('book_keywords.on_moderation', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(__('book_keyword.on_check'), route('book_keywords.on_moderation'));
});

Breadcrumbs::for('complaints.index', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(trans_choice('complain.complains', 2), route('complaints.index'));
});

Breadcrumbs::for('comments.on_check', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(trans_choice('comment.on_check', 2), route('comments.on_check'));
});

Breadcrumbs::for('posts.on_check', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(trans_choice('post.on_check', 2), route('posts.on_check'));
});

Breadcrumbs::for('groups.index', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(trans_choice('user_group.user_groups', 2), route('groups.index'));
});

Breadcrumbs::for('groups.create', function ($breadcrumbs) {
	$breadcrumbs->parent('groups.index');
	$breadcrumbs->push(__('common.create'), route('groups.create'));
});

Breadcrumbs::for('groups.edit', function ($breadcrumbs, $group) {

	if (is_object($group)) {
		$breadcrumbs->parent('groups.index');
		$breadcrumbs->push($group->name);
		$breadcrumbs->push(__('common.edit'), route('groups.edit', $group));
	}
});

Breadcrumbs::for('users.on_moderation', function ($breadcrumbs) {

	$breadcrumbs->parent('users');
	$breadcrumbs->push(__('user.on_moderation'), route('users.on_moderation'));
});

Breadcrumbs::for('users.auth_fails', function ($breadcrumbs, $user) {

	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('user.auth_fails'), route('users.auth_fails', $user));
});

Breadcrumbs::for('users.auth_logs', function ($breadcrumbs, $user) {

	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('user.logs'), route('users.auth_logs', $user));
});

Breadcrumbs::for('home.latest_books', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(__('home.lastest_books'), route('home.latest_books'));
});

Breadcrumbs::for('home.popular_books', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(__('home.popular_books'), route('home.popular_books'));

	$period = request('period') ?: 'day';

	switch ($period) {
		case 'day':
			$breadcrumbs->push(__('home.popular_books_range.for_day'), route('home.popular_books', ['period' => 'day']));
			break;
		case 'week':
			$breadcrumbs->push(__('home.popular_books_range.for_week'), route('home.popular_books', ['period' => 'week']));
			break;
		case 'month':
			$breadcrumbs->push(__('home.popular_books_range.for_month'), route('home.popular_books', ['period' => 'month']));
			break;
		case 'year':
			$breadcrumbs->push(__('home.popular_books_range.for_year'), route('home.popular_books', ['period' => 'year']));
			break;
	}
});

Breadcrumbs::for('home.latest_comments', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(__('home.latest_comments'), route('home.latest_comments'));
});

Breadcrumbs::for('home.latest_posts', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(__('home.latest_posts'), route('home.latest_posts'));
});

Breadcrumbs::for('text_blocks.show', function ($breadcrumbs, $name, $id) {
	//$breadcrumbs->parent('home');
	//$breadcrumbs->push(trans_choice('text_block.text_blocks', 1));
	//$breadcrumbs->push($textBlock->name, route('text_blocks.show', $textBlock));
});

Breadcrumbs::for('text_blocks.edit', function ($breadcrumbs, $textBlock) {
	//$breadcrumbs->parent('text_blocks.show', $textBlock);
	//$breadcrumbs->push(__('common.edit'), route('text_blocks.edit', $textBlock));
});

Breadcrumbs::for('invitation', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(__('auth.registration'), route('invitation'));
});

Breadcrumbs::for('users.registration', function ($breadcrumbs, $token) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(__('user.register'), route('users.registration', $token));
});

Breadcrumbs::for('admin_notes.index', function ($breadcrumbs) {

	$type = request()->type;
	$id = request()->id;

	switch ($type) {
		case 'book':
			$object = App\Book::any()->find($id);
			$breadcrumbs->parent('books.show', $object);
			break;
		case 'author':
			$object = App\Author::any()->find($id);
			$breadcrumbs->parent('authors.show', $object);
			break;
		case 'user':
			$object = App\User::any()->find($id);
			$breadcrumbs->parent('profile', $object);
			break;
	}

	$breadcrumbs->push(trans_choice('admin_note.admin_notes', 2), route('admin_notes.index', compact('type', 'id')));
});

Breadcrumbs::for('admin_notes.create', function ($breadcrumbs) {

	$type = request()->type;
	$id = request()->id;

	switch ($type) {
		case 'book':
			$object = App\Book::any()->find($id);
			$breadcrumbs->parent('books.show', $object);
			break;
		case 'author':
			$object = App\Author::any()->find($id);
			$breadcrumbs->parent('authors.show', $object);
			break;
		case 'user':
			$object = App\User::any()->find($id);
			$breadcrumbs->parent('profile', $object);
			break;
	}

	$breadcrumbs->push(__('admin_note.create'), route('admin_notes.create', compact('type', 'id')));
});

Breadcrumbs::for('admin_notes.edit', function ($breadcrumbs, $admin_note) {

	if ($admin_note instanceof \App\AdminNote) {
		$admin_noteable = $admin_note->admin_noteable()->any()->first();

		switch ($admin_noteable->admin_notes()->getMorphClass()) {
			case 'book':
				$breadcrumbs->parent('books.show', $admin_noteable);
				break;
			case 'author':
				$breadcrumbs->parent('authors.show', $admin_noteable);
				break;
			case 'user':
				$breadcrumbs->parent('profile', $admin_noteable);
				break;
		}

		$breadcrumbs->push(__('admin_note.edit'), route('admin_notes.edit', compact('admin_note')));
	}
});

Breadcrumbs::for('password.request', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(__('password.reset'), route('password.request'));
});

Breadcrumbs::for('password.reset_form', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(__('password.change'));
});

Breadcrumbs::for('authors.group.index', function ($breadcrumbs, $author) {
	$breadcrumbs->parent('authors.show', $author);
	$breadcrumbs->push(__('author.in_group'), route('authors.group.index', $author));
});

Breadcrumbs::for('authors.create', function ($breadcrumbs) {
	$breadcrumbs->parent('authors');
	$breadcrumbs->push(__('common.create'), route('authors.create'));
});

Breadcrumbs::for('users.books.created', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(trans_choice('book.books', 2), route('books'));
	$breadcrumbs->push(trans_choice('common.added', 2), route('users.books.created', $user));
});

Breadcrumbs::for('users.authors.created', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(trans_choice('author.authors', 2), route('authors'));
	$breadcrumbs->push(trans_choice('common.added', 2), route('users.authors.created', $user));
});

Breadcrumbs::for('users.sequences.created', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(trans_choice('sequence.sequences', 2), route('sequences'));
	$breadcrumbs->push(trans_choice('common.added', 2), route('users.sequences.created', $user));
});

Breadcrumbs::for('bookmark_folders.show', function ($breadcrumbs, $bookmarkFolder) {

	if (is_numeric($bookmarkFolder))
		$bookmarkFolder = App\BookmarkFolder::find($bookmarkFolder);

	if (!empty($bookmarkFolder)) {
		$breadcrumbs->parent('users.bookmarks.index', $bookmarkFolder->create_user);

		$breadcrumbs->push($bookmarkFolder->title, route('bookmark_folders.show', $bookmarkFolder));
	}
});

Breadcrumbs::for('bookmarks.edit', function ($breadcrumbs, $bookmark) {

	if (is_object($bookmark)) {
		if (!empty($bookmark->folder))
			$breadcrumbs->parent('bookmark_folders.show', $bookmark->folder);
		else
			$breadcrumbs->parent('users.bookmarks.index', $bookmark->create_user);

		$breadcrumbs->push(__('common.edit'), route('bookmarks.edit', $bookmark));
	}
});

Breadcrumbs::for('bookmark_folders.edit', function ($breadcrumbs, $bookmarkFolder) {

	$breadcrumbs->parent('bookmark_folders.show', $bookmarkFolder);
	$breadcrumbs->push(__('common.edit'), route('bookmark_folders.edit', $bookmarkFolder));
});

Breadcrumbs::for('author_repeats.index', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(__('navbar.author_repeats'), route('author_repeats.index'));
});

Breadcrumbs::for('author_repeats.create', function ($breadcrumbs) {
	$breadcrumbs->parent('author_repeats.index');
	$breadcrumbs->push(__('common.add'), route('author_repeats.create'));
});

Breadcrumbs::for('author_repeats.edit', function ($breadcrumbs, $authorRepeat) {
	$breadcrumbs->parent('author_repeats.index');
	$breadcrumbs->push(__('common.edit'), route('author_repeats.edit', $authorRepeat));
});

Breadcrumbs::for('authors.merge', function ($breadcrumbs) {
	$breadcrumbs->parent('author_repeats.index');
	$breadcrumbs->push(__('common.merge'), route('authors.merge'));
});

Breadcrumbs::for('books.files.show', function ($breadcrumbs, $book, $file) {
	$breadcrumbs->parent('books.show', $book);
});

Breadcrumbs::for('books.files.edit', function ($breadcrumbs, $book, $file) {
	$breadcrumbs->parent('books.show', $book);

	if (is_numeric($file))
		$file = App\BookFile::find($file);

	if (!empty($file)) {
		$breadcrumbs->push($file->name);
		$breadcrumbs->push(__('common.edit'), route('books.files.edit', compact('book', 'file')));
	}
});

Breadcrumbs::for('books.files.create', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('common.create'), route('books.files.create', compact('book')));
});

Breadcrumbs::for('complains.report', function ($breadcrumbs, $type, $id) {
	$breadcrumbs->parent('complaints.index');
	$breadcrumbs->push(__('common.send'), route('complains.report', compact('type', 'id')));
});

Breadcrumbs::for('complaints.show', function ($breadcrumbs, $complain) {

	if (!empty($complain->complainable)) {
		if ($complain->complainable instanceof \App\Book)
			$breadcrumbs->parent('books.show', $complain->complainable);
	}

	$breadcrumbs->push(__('complain.complain'), route('complaints.show', $complain));
});

Breadcrumbs::for('books.comments.create', function ($breadcrumbs, $book) {

	$breadcrumbs->parent('books.show', $book);

	if (is_numeric(request()->parent))
		$parent = App\Comment::find(request()->parent);

	if (!empty($parent)) {
		$breadcrumbs->push(trans_choice('comment.comments', 2), route('comments.go', ['comment' => $parent]));
		$breadcrumbs->push(__('common.reply'), route('books.comments.create', compact('book', 'parent')));
	} else
		$breadcrumbs->push(__('common.send'), route('books.comments.create', $book));
});

Breadcrumbs::for('comments.edit', function ($breadcrumbs, $comment) {

	if (is_object($comment)) {
		if ($comment->commentable_type == 'book') {
			$breadcrumbs->parent('books.show', $comment->commentable);
		}

		$breadcrumbs->push(trans_choice('comment.comments', 1), route('comments.go', ['comment' => $comment]));

		$breadcrumbs->push(__('common.edit'), route('comments.edit', ['comment' => $comment]));
	}
});

Breadcrumbs::for('authors.verification.request', function ($breadcrumbs, $author) {
	$breadcrumbs->parent('authors.show', $author);
	$breadcrumbs->push(__('author.sent_request'), route('authors.verification.request', ['author' => $author]));
});

Breadcrumbs::for('authors.editor.request', function ($breadcrumbs, $author) {
	$breadcrumbs->parent('authors.show', $author);
	$breadcrumbs->push(__('author.sent_request'), route('authors.editor.request', ['author' => $author]));
});

Breadcrumbs::for('books.on_moderation', function ($breadcrumbs) {
	$breadcrumbs->parent('books');
	$breadcrumbs->push(__('common.on_moderation'), route('books.on_moderation'));
});

Breadcrumbs::for('posts.create', function ($breadcrumbs, $topic) {

	if ($topic instanceof \App\Topic)
		$breadcrumbs->parent('topics.show', $topic);

	if (is_numeric(request()->parent))
		$parent = App\Post::find(request()->parent);

	if (!empty($parent)) {
		$breadcrumbs->push(trans_choice('comment.comments', 1), route('posts.go_to', ['post' => $parent]));
		$breadcrumbs->push(__('common.reply'), route('posts.create', compact('topic', 'parent')));
	} else
		$breadcrumbs->push(__('common.send'), route('posts.create', $topic));
});

Breadcrumbs::for('forum_groups.edit', function ($breadcrumbs, $forum_group) {

	$breadcrumbs->parent('forums.index');

	if (is_object($forum_group)) {
		$breadcrumbs->push($forum_group->name);

		$breadcrumbs->push(__('common.edit'), route('forum_groups.edit', $forum_group));
	}
});

Breadcrumbs::for('sequences.merge_form', function ($breadcrumbs, $sequence) {

	$breadcrumbs->parent('sequences.show', $sequence);

	//$breadcrumbs->push('Присоединить серию к другой', route('sequences.merge_form', $sequence));
});

Breadcrumbs::for('comments.create_reply', function ($breadcrumbs, $comment) {

	switch ($comment->commentable_type) {
		case 'book':
			$commentable = App\Book::find($comment->commentable_id);
			if (!empty($commentable))
				$breadcrumbs->parent('books.show', $commentable);
			break;
	}

	$breadcrumbs->push(__('common.reply'), route('comments.create_reply', $comment));
});
/*
Breadcrumbs::after(function ($breadcrumbs) {
    $page = (int) request('page', 1);
    if ($page > 1) {
        $breadcrumbs->push('Страница '.$page.'', null, ['current' => false]);
    }
});
*/
Breadcrumbs::for('achievements.index', function ($breadcrumbs) {
	//$breadcrumbs->parent('home');
	$breadcrumbs->push(trans_choice('achievement.achievements', 2), route('achievements.index'));
});

Breadcrumbs::for('achievements.create', function ($breadcrumbs) {
	$breadcrumbs->parent('achievements.index');
	$breadcrumbs->push(__('common.create'), route('achievements.create'));
});

Breadcrumbs::for('achievements.edit', function ($breadcrumbs, $achievement) {
	$breadcrumbs->parent('achievements.index');
	$breadcrumbs->push(__('common.edit'), route('achievements.edit', $achievement));
});

Breadcrumbs::for('users.achievements', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(trans_choice('achievement.achievements', 2), route('users.achievements', $user));
});

Breadcrumbs::for('achievements.show', function ($breadcrumbs, $achievement) {
	$breadcrumbs->parent('achievements.index');

	if (is_object($achievement)) {
		$breadcrumbs->push($achievement->title, route('achievements.show', $achievement));
	}
});


Breadcrumbs::for('allowance', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('user_setting.allowance'), route('allowance', $user));
});

Breadcrumbs::for('email_delivery', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('user.email_delivery'), route('email_delivery', $user));
});

Breadcrumbs::for('users.emails.index', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('user.emails'), route('users.emails.index', $user));
});

Breadcrumbs::for('users.emails.create', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('users.emails.index', $user);
	$breadcrumbs->push(__('common.add'), route('users.emails.create', ['user' => $user]));
});

Breadcrumbs::for('users.social_accounts.index', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('user.social_accounts'), route('users.social_accounts.index', $user));
});

Breadcrumbs::for('users.settings.read_style', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('user.read_style'), route('users.settings.read_style', $user));
});

Breadcrumbs::for('genre_blacklist', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('user.genre_blacklist'), route('genre_blacklist', $user));
});

Breadcrumbs::for('users.bookmarks.index', function ($breadcrumbs, $user) {
	$breadcrumbs->push(__('bookmark.all'), route('users.bookmarks.index', $user));
});

Breadcrumbs::for('users.subscriptions.comments', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('user.subscriptions_comments'), route('users.subscriptions.comments', $user));
});

Breadcrumbs::for('users.authors.books', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('users.authors', $user);
	$breadcrumbs->push(__('user.authors_books'), route('users.authors.books', $user));
});

Breadcrumbs::for('keywords.index', function ($breadcrumbs) {
	$breadcrumbs->push(__('book.keywords'), route('keywords.index'));
});

Breadcrumbs::for('keywords.edit', function ($breadcrumbs) {
	$breadcrumbs->parent('keywords.index');
});

Breadcrumbs::for('keywords.create', function ($breadcrumbs) {
	$breadcrumbs->parent('keywords.index');
});


Breadcrumbs::for('users.notes.index', function ($breadcrumbs, $user) {
	$breadcrumbs->push(trans_choice('user_note.user_notes', 2), route('users.notes.index', $user));
});

Breadcrumbs::for('users.notes.create', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('users.notes.index', $user);
	$breadcrumbs->push(__('common.create'), route('users.notes.create', $user));
});

Breadcrumbs::for('users.notifications.index', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('notification.notification'), route('users.notifications.index', $user));
});

Breadcrumbs::for('users.settings.notifications', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('user_setting.notifications'), route('users.settings.notifications', $user));
});

Breadcrumbs::for('notes.edit', function ($breadcrumbs, $note) {

	if (!empty($note->create_user))
		$breadcrumbs->parent('users.notes.index', $note->create_user);

	$breadcrumbs->push(__('common.edit'), route('notes.edit', $note));
});

Breadcrumbs::for('notes.show', function ($breadcrumbs, $note) {

	if (!empty($note) and !empty($note->create_user)) {
		$breadcrumbs->parent('users.notes.index', $note->create_user);
		$breadcrumbs->push(trans_choice('user_note.user_notes', 2), route('notes.show', $note));
	}
});

Breadcrumbs::after(function ($trail) {
	$page = (int)request('page', 1);

	if (Browser::isBot()) {
		if ($page > 1)
			$trail->push(__('common.page') . " $page", \Illuminate\Support\Facades\URL::full(), ['current' => false]);
	}
});

Breadcrumbs::macro('pageTitle', function () {
	$title = ':: ';

	$home_url = route('home');

	$breadcrumbs = Breadcrumbs::generate()->reverse()->where('current', '!==', false)->filter(function ($value, $key) use ($home_url) {
		return $value->url != $home_url;
	});

	foreach ($breadcrumbs as $breadcrumb) {
		$title .= $breadcrumb->title . ' - ';
	}

	if (Browser::isBot()) {
		if (($page = (int)request('page')) > 1)
			$title .= __('common.page') . " $page - ";
	}

	$title .= __('seo.title');

	return $title;
});

Breadcrumbs::macro('bookmarkTitle', function () {

	$title = '';

	$home_url = route('home');

	$breadcrumbs = Breadcrumbs::generate()->reverse()->where('current', '!==', false)->filter(function ($value, $key) use ($home_url) {
		return $value->url != $home_url;
	});

	$array = [];

	if (($page = (int)request('page')) > 1)
		$array[] = __('common.page') . " $page";

	foreach ($breadcrumbs as $breadcrumb) {
		$array[] = $breadcrumb->title;
	}

	$title = implode(' - ', $array);

	return $title;
});

Breadcrumbs::for('users.wallet', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('user_payment_detail.wallet'), route('users.wallet', $user));
});

Breadcrumbs::for('users.wallet.deposit', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('users.wallet', $user);
	$breadcrumbs->push(__('user_payment_transaction.deposit_balance'), route('users.wallet.deposit', $user));
});

Breadcrumbs::for('users.wallet.withdrawal', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('users.wallet', $user);
	$breadcrumbs->push(__('user_payment_transaction.withdrawal_from_balance'), route('users.wallet.withdrawal', $user));
});

Breadcrumbs::for('users.wallet.payment_details', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('users.wallet', $user);
	$breadcrumbs->push(__('user_payment_detail.details'), route('users.wallet.payment_details', $user));
});

Breadcrumbs::for('users.books.purchased', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('book.purchased_books'), route('users.books.purchased', $user));
});

Breadcrumbs::for('users.wallet.transfer', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('users.wallet', $user);
	$breadcrumbs->push(__('user_money_transfer.transfer_money_to_user'), route('users.wallet.transfer', $user));
});

Breadcrumbs::for('financial_statistic.index', function ($breadcrumbs) {
	$breadcrumbs->push(__('financial_statistic.financial_statistic'), route('financial_statistic.index'));
});

Breadcrumbs::for('financial_statistic.all_transactions', function ($breadcrumbs) {
	$breadcrumbs->parent('financial_statistic.index');
	$breadcrumbs->push(__('financial_statistic.all_transactions'), route('financial_statistic.all_transactions'));
});

Breadcrumbs::for('authors.sales.request', function ($breadcrumbs, $author) {
	$breadcrumbs->parent('authors.show', $author);
	$breadcrumbs->push(__('author_sale_request.request'), route('authors.sales.request', ['author' => $author]));
});

Breadcrumbs::for('authors.sales_requests.index', function ($breadcrumbs) {
	$breadcrumbs->push(__('author_sale_request.requests'), route('authors.sales_requests.index'));
});

Breadcrumbs::for('authors.sales_requests.show', function ($breadcrumbs, $request) {
	$breadcrumbs->parent('authors.sales_requests.index');
});

Breadcrumbs::for('books.sales.edit', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('book.sales'), route('books.sales.edit', $book));
});

Breadcrumbs::for('books.access.edit', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('book.access_settings'), route('books.access.edit', $book));
});

Breadcrumbs::for('books.users.bought', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('book.users_bought'), route('books.users.bought', $book));
});

Breadcrumbs::for('users.referred.users', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->push(__('user.referred_users'), route('users.referred.users', $user));
});

Breadcrumbs::for('users.refer', function ($breadcrumbs) {
	$breadcrumbs->push(__('user.how_refer_users'), route('users.refer'));
});

Breadcrumbs::for('books.purchase', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('book.choice_of_payment_method'), route('books.purchase', $book));
});

Breadcrumbs::for('purchase_rules', function ($breadcrumbs) {
	$breadcrumbs->push(__('text_block.purchase_rules'), route('purchase_rules'));
});

Breadcrumbs::for('sales_rules', function ($breadcrumbs) {
	$breadcrumbs->push(__('text_block.sales_rules'), route('sales_rules'));
});

Breadcrumbs::for('topics.archived', function ($breadcrumbs) {
	$breadcrumbs->push(__('topic.archived_topics'), route('topics.archived'));
});

Breadcrumbs::for('rules', function ($breadcrumbs) {
	$breadcrumbs->push(__('text_block.rules'), route('rules'));
});

Breadcrumbs::for('rules_publish_books', function ($breadcrumbs) {
	$breadcrumbs->push(__('text_block.rules_publish_books'), route('rules_publish_books'));
});

Breadcrumbs::for('personal_data_processing_agreement', function ($breadcrumbs) {
	$breadcrumbs->push(__('text_block.personal_data_processing_agreement'), route('personal_data_processing_agreement'));
});

Breadcrumbs::for('collections.index', function ($breadcrumbs) {
	$breadcrumbs->push(__('collection.collections'), route('collections.index'));
});

Breadcrumbs::for('collections.show', function ($breadcrumbs, $collection) {
	if ($collection instanceof Collection) {
		$breadcrumbs->parent('collections.index');
		$breadcrumbs->push($collection->title, route('collections.show', $collection));
	}
});

Breadcrumbs::for('collections.books', function ($breadcrumbs, $collection) {
	if ($collection instanceof Collection) {
		$breadcrumbs->parent('collections.show', $collection);
		$breadcrumbs->push(trans_choice('collection.breadcrumbs.books', $collection->books_count), route('collections.books', $collection));
	}
});

Breadcrumbs::for('collections.comments', function ($breadcrumbs, $collection) {
	if ($collection instanceof Collection) {
		$breadcrumbs->parent('collections.show', $collection);
		$breadcrumbs->push(trans_choice('collection.breadcrumbs.comments', $collection->comments_count), route('collections.comments', $collection));
	}
});

Breadcrumbs::for('collections.users.index', function ($breadcrumbs, $collection) {
	if ($collection instanceof Collection) {
		$breadcrumbs->parent('collections.show', $collection);
		$breadcrumbs->push(trans_choice('collection.breadcrumbs.users', $collection->users_count), route('collections.users.index', $collection));
	}
});

Breadcrumbs::for('collections.users.create', function ($breadcrumbs, $collection) {
	$breadcrumbs->parent('collections.users.index', $collection);
	$breadcrumbs->push(__('collection.add_user'), route('collections.users.create', $collection));
});

Breadcrumbs::for('collections.users.edit', function ($breadcrumbs, $collection, $user) {
	$breadcrumbs->parent('collections.users.index', $collection);
	$breadcrumbs->push($user->userName, route('profile', $user));
	$breadcrumbs->push(__('collection.breadcrumbs.edit_user'), route('collections.users.edit', ['collection' => $collection, 'user' => $user]));
});

Breadcrumbs::for('collections.create', function ($breadcrumbs) {
	$breadcrumbs->parent('collections.index');
	$breadcrumbs->push(__('collection.create_collection'), route('collections.create'));
});

Breadcrumbs::for('collections.edit', function ($breadcrumbs, $collection) {
	$breadcrumbs->parent('collections.show', $collection);
	$breadcrumbs->push(__('collection.edit_collection'), route('collections.edit', $collection));
});

Breadcrumbs::for('collections.delete.confirmation', function ($breadcrumbs, $collection) {
	$breadcrumbs->parent('collections.show', $collection);
	$breadcrumbs->push(__('Confirm the deletion'), route('collections.delete.confirmation', $collection));
});

Breadcrumbs::for('collections.books.select', function ($breadcrumbs, $collection) {
	$breadcrumbs->parent('collections.books', $collection);
	$breadcrumbs->push(__('collection.attach_book'), route('collections.books.select', $collection));
});

Breadcrumbs::for('collections.books.edit', function ($breadcrumbs, $collection, $book) {
	$breadcrumbs->parent('collections.books', $collection);
	$breadcrumbs->push(__('collection.edit_description'), route('collections.books.edit', compact('collection', 'book')));
});

Breadcrumbs::for('users.collections.created', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->parent('collections.index');
	$breadcrumbs->push(__('collection.created'), route('users.collections.created', $user));
});

Breadcrumbs::for('users.collections.favorite', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('profile', $user);
	$breadcrumbs->parent('collections.index');
	$breadcrumbs->push(__('collection.favorite'), route('users.collections.favorite', $user));
});

Breadcrumbs::for('authors.how_to_start_selling_books', function ($breadcrumbs) {
	$breadcrumbs->push(__('author_sale_request.instructions_for_writers'));
	$breadcrumbs->push(__('author_sale_request.interactive_instructions_for_authors'));
});

Breadcrumbs::for('books.replace_book_created_by_another_user.form', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
});

Breadcrumbs::for('books.editions.index', function ($breadcrumbs, $book) {

	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('book.in_group'), route('books.editions.index', $book));
});

Breadcrumbs::for('books.text_processings.index', function ($breadcrumbs, $book) {

	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('book.text_processings'), route('books.text_processings.index', $book));
});

Breadcrumbs::for('books.text_processings.create', function ($breadcrumbs, $book) {

	$breadcrumbs->parent('books.text_processings.index', $book);
	$breadcrumbs->push(__('common.create'), route('books.text_processings.create', $book));
});

Breadcrumbs::for('books.delete.form', function ($breadcrumbs, $book) {

	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('book.delete_a_book'), route('books.delete.form', $book));
});

Breadcrumbs::for('books.activity_logs', function ($breadcrumbs, $book) {

	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('book.event_log'), route('books.activity_logs', $book));
});

Breadcrumbs::for('ideas.index', function ($breadcrumbs) {
	$breadcrumbs->push(__('idea.ideas_for_improving_litlife'), route('ideas.index'));
});

Breadcrumbs::for('surveys.create', function ($breadcrumbs) {
	$breadcrumbs->push(__('survey.survey'), route('surveys.create'));
});

Breadcrumbs::for('surveys.index', function ($breadcrumbs) {
	$breadcrumbs->push(__('survey.survey_result'), route('surveys.index'));
});

Breadcrumbs::for('surveys.store', function ($breadcrumbs) {
	$breadcrumbs->push(__('survey.survey'), route('surveys.store'));
});

Breadcrumbs::for('faq', function ($breadcrumbs) {
	$breadcrumbs->push(__('Answers to frequently asked questions'), route('faq'));
});

Breadcrumbs::for('ad_blocks.index', function ($breadcrumbs) {
	$breadcrumbs->push(__('Ad blocks'), route('ad_blocks.index'));
});

Breadcrumbs::for('ad_blocks.create', function ($breadcrumbs) {
	$breadcrumbs->parent('ad_blocks.index');
	$breadcrumbs->push(__('Create'), route('ad_blocks.create'));
});

Breadcrumbs::for('ad_blocks.edit', function ($breadcrumbs, $adBlock) {
	$breadcrumbs->parent('ad_blocks.index');

	if ($adBlock instanceof \App\AdBlock)
		$breadcrumbs->push(__('Edit'), route('ad_blocks.edit', ['ad_block' => $adBlock->id]));
});

Breadcrumbs::for('books.collections.index', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('In the collections'), route('books.collections.index', ['book' => $book]));
});

Breadcrumbs::for('books.collections.create', function ($breadcrumbs, $book) {
	$breadcrumbs->parent('books.show', $book);
	$breadcrumbs->push(__('Add a book to a collection'), route('books.collections.create', ['book' => $book]));
});

Breadcrumbs::for('users.support_questions.index', function ($breadcrumbs, $user) {
	$breadcrumbs->push(__('Support questions'), route('users.support_questions.index', $user));
});

Breadcrumbs::for('support_questions.create', function ($breadcrumbs, $user) {
	$breadcrumbs->parent('users.support_questions.index', ['user' => $user]);
	$breadcrumbs->push(__('New question'), route('support_questions.create', $user));
});

Breadcrumbs::for('support_questions.show', function ($breadcrumbs, $supportQuestion) {

	if ($supportQuestion instanceof \App\SupportQuestion) {
		if (\Illuminate\Support\Facades\Auth::user()->is($supportQuestion->create_user)) {
			$breadcrumbs->parent('users.support_questions.index', ['user' => $supportQuestion->create_user]);
		} else {
			if ($supportQuestion->isAccepted())
				$breadcrumbs->parent('support_questions.solved');
			elseif ($supportQuestion->isReviewStarts())
				$breadcrumbs->parent('support_questions.in_process_of_solving');
			elseif ($supportQuestion->isSentForReview())
				$breadcrumbs->parent('support_questions.unsolved');
		}

		$breadcrumbs->push($supportQuestion->title, route('support_questions.show', ['support_question' => $supportQuestion]));
	}
});

Breadcrumbs::for('support_questions.unsolved', function ($breadcrumbs) {
	$breadcrumbs->parent('support_questions.index');
	$breadcrumbs->push(__('New questions'), route('support_questions.unsolved'));
});

Breadcrumbs::for('support_questions.solved', function ($breadcrumbs) {
	$breadcrumbs->parent('support_questions.index');
	$breadcrumbs->push(__('Solved questions'), route('support_questions.solved'));
});

Breadcrumbs::for('support_questions.in_process_of_solving', function ($breadcrumbs) {
	$breadcrumbs->parent('support_questions.index');
	$breadcrumbs->push(__('In process'), route('support_questions.in_process_of_solving'));
});

Breadcrumbs::for('support_questions.index', function ($breadcrumbs) {
	$breadcrumbs->push(__('Support questions'), route('support_questions.index'));
});

Breadcrumbs::for('support_questions.edit', function ($breadcrumbs, $supportQuestion) {
    $breadcrumbs->push(__('Editing a question'), route('support_questions.edit', $supportQuestion));
});

Breadcrumbs::for('verifications.show', function ($breadcrumbs, $manager) {
    $breadcrumbs->push(__('Request for verification'), route('support_questions.edit', $manager));
});

Breadcrumbs::for('remove_ads', function ($breadcrumbs) {
    $breadcrumbs->push(__('How to disable ads?'), route('remove_ads'));
});