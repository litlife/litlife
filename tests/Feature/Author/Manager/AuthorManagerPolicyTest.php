<?php

namespace Tests\Feature\Author\Manager;

use App\Attachment;
use App\Author;
use App\Book;
use App\BookFile;
use App\BookKeyword;
use App\Manager;
use App\Section;
use App\User;
use Tests\TestCase;

class AuthorManagerPolicyTest extends TestCase
{
    public function testPolicy()
    {
        $user = User::factory()->with_user_group()->create();

        $manager = Manager::factory()->create();

        $this->assertFalse($user->can('view', $manager));
        $this->assertFalse($user->can('viewOnCheck', Manager::class));
        $this->assertFalse($user->can('create', Manager::class));
        $this->assertFalse($user->can('request', Manager::class));
        $this->assertFalse($user->can('delete', $manager));
        $this->assertFalse($user->can('restore', $manager));
        $this->assertFalse($user->can('approve', $manager));
        $this->assertFalse($user->can('decline', $manager));

        $user->group->moderator_add_remove = false;
        $user->group->author_editor_check = true;
        $user->group->push();

        $this->assertFalse($user->can('view', $manager));
        $this->assertTrue($user->can('viewOnCheck', Manager::class));
        $this->assertFalse($user->can('create', Manager::class));
        $this->assertFalse($user->can('request', Manager::class));
        $this->assertFalse($user->can('delete', $manager));
        $this->assertFalse($user->can('restore', $manager));
        $this->assertFalse($user->can('approve', $manager));
        $this->assertFalse($user->can('decline', $manager));

        $user->group->author_editor_check = false;
        $user->group->moderator_add_remove = true;
        $user->group->push();

        $this->assertTrue($user->can('view', $manager));
        $this->assertTrue($user->can('create', Manager::class));
        $this->assertFalse($user->can('viewOnCheck', Manager::class));
        $this->assertFalse($user->can('request', Manager::class));
        $this->assertTrue($user->can('delete', $manager));
        $this->assertFalse($user->can('restore', $manager));
        $this->assertFalse($user->can('approve', $manager));
        $this->assertFalse($user->can('decline', $manager));

        $user->group->author_editor_check = false;
        $user->group->moderator_add_remove = false;
        $user->group->author_editor_request = true;
        $user->group->push();

        $this->assertFalse($user->can('view', $manager));
        $this->assertFalse($user->can('create', Manager::class));
        $this->assertFalse($user->can('viewOnCheck', Manager::class));
        $this->assertTrue($user->can('request', Manager::class));
        $this->assertFalse($user->can('delete', $manager));
        $this->assertFalse($user->can('restore', $manager));
        $this->assertFalse($user->can('approve', $manager));
        $this->assertFalse($user->can('decline', $manager));

        $user->group->author_editor_check = false;
        $user->group->moderator_add_remove = false;
        $user->group->author_editor_request = false;
        $user->group->push();

        $manager->user_id = $user->id;
        $manager->save();

        $this->assertTrue($user->can('view', $manager));
        $this->assertFalse($user->can('create', Manager::class));
        $this->assertFalse($user->can('viewOnCheck', Manager::class));
        $this->assertFalse($user->can('request', Manager::class));
        $this->assertTrue($user->can('delete', $manager));
        $this->assertFalse($user->can('restore', $manager));
        $this->assertFalse($user->can('approve', $manager));
        $this->assertFalse($user->can('decline', $manager));

        $manager->can_sale = true;
        $manager->save();
        $manager->refresh();

        $this->assertTrue($user->can('view', $manager));
        $this->assertFalse($user->can('create', Manager::class));
        $this->assertFalse($user->can('viewOnCheck', Manager::class));
        $this->assertFalse($user->can('request', Manager::class));
        $this->assertFalse($user->can('delete', $manager));
        $this->assertFalse($user->can('restore', $manager));
        $this->assertFalse($user->can('approve', $manager));
        $this->assertFalse($user->can('decline', $manager));
    }

    public function testAuthorCharacterPolicy()
    {
        $user = User::factory()->with_user_group()->create();

        $author = Author::factory()->create();

        $manager = Manager::factory()->create([
            'character' => 'author',
            'user_id' => $user->id,
            'manageable_id' => $author->id
        ]);

        $this->assertTrue($user->can('manage', $author));
        $this->assertTrue($user->can('update', $author));
        $this->assertFalse($user->can('merge', $author));
        $this->assertFalse($user->can('delete', $author));
        $this->assertFalse($user->can('restore', $author));
        $this->assertTrue($user->can('create_photo', $author));
        $this->assertFalse($user->can('group', $author));
        $this->assertFalse($user->can('ungroup', $author));
        $this->assertFalse($user->can('watch_activity_logs', $author));
        $this->assertFalse($user->can('display_technical_information', $author));
        $this->assertTrue($user->can('refresh_counters', $author));
        $this->assertFalse($user->can('makeAccepted', $author));
        $this->assertFalse($user->can('booksCloseAccess', $author));
    }

    public function testEditorCharacterPolicy()
    {
        $user = User::factory()->with_user_group()->create();

        $author = Author::factory()->create();

        $manager = Manager::factory()->create([
            'character' => 'editor',
            'user_id' => $user->id,
            'manageable_id' => $author->id
        ]);

        $this->assertTrue($user->can('manage', $author));
        $this->assertTrue($user->can('update', $author));
        $this->assertFalse($user->can('merge', $author));
        $this->assertFalse($user->can('delete', $author));
        $this->assertFalse($user->can('restore', $author));
        $this->assertTrue($user->can('create_photo', $author));
        $this->assertFalse($user->can('group', $author));
        $this->assertFalse($user->can('ungroup', $author));
        $this->assertFalse($user->can('watch_activity_logs', $author));
        $this->assertFalse($user->can('display_technical_information', $author));
        $this->assertFalse($user->can('refresh_counters', $author));
        $this->assertFalse($user->can('makeAccepted', $author));
        $this->assertFalse($user->can('booksCloseAccess', $author));
    }

    public function testIfVerifiedAuthorAndBookIsBookHasNotBeenPublishedByPublisher()
    {
        $user = User::factory()->with_user_group()->create();
        $user->group->add_comment = true;
        $user->group->book_similar_vote = true;
        $user->group->book_keyword_vote = true;
        $user->push();
        $user->refresh();

        $author = Author::factory()->create();

        $manager = Manager::factory()->create([
            'character' => 'author',
            'user_id' => $user->id,
            'manageable_id' => $author->id
        ]);

        $this->assertEquals('author', $manager->character);
        $this->assertTrue($user->can('manage', $author));

        $book = Book::factory()->with_cover()->si_false()->lp_false()->publish_fields_empty()->create(['create_user_id' => $user->id]);
        $book->writers()->sync([$author->id]);
        $book->user_vote_count = 1;
        $book->save();

        $file = BookFile::factory()->txt()->create(['book_id' => $book->id]);

        $section = Section::factory()->create(['book_id' => $book->id]);

        $cover = $book->cover;

        $attachment = Attachment::factory()->create(['book_id' => $book->id]);

        $this->assertTrue($cover->isCover());
        $this->assertFalse($attachment->isCover());

        $book->refresh();
        $this->assertFalse($book->isEditionDetailsFilled());

        $this->assertTrue($user->can('update', $book));
        $this->assertFalse($user->can('publish', $book));
        $this->assertFalse($user->can('addToPrivate', $book));
        $this->assertFalse($user->can('delete', $book));
        $this->assertFalse($user->can('restore', $book));
        $this->assertTrue($user->can('group', $book));
        $this->assertTrue($user->can('ungroup', $book));
        $this->assertTrue($user->can('make_main_in_group', $book));
        $this->assertTrue($user->can('change_access', $book));
        $this->assertTrue($user->can('commentOn', $book));
        $this->assertTrue($user->can('add_similar_book', $book));
        $this->assertTrue($user->can('view_section_list', $book));
        $this->assertFalse($user->can('retry_failed_parse', $book));
        $this->assertTrue($user->can('remove_cover', $book));
        $this->assertFalse($user->can('view_on_moderation', $book));
        $this->assertTrue($user->can('view_group_books', $book));
        $this->assertFalse($user->can('watch_activity_logs', $book));
        $this->assertFalse($user->can('display_technical_information', $book));
        $this->assertTrue($user->can('refresh_counters', $book));
        $this->assertFalse($user->can('open_comments', $book));
        $this->assertFalse($user->can('close_comments', $book));
        $this->assertTrue($user->can('read', $book));
        $this->assertTrue($user->can('view_download_files', $book));
        $this->assertTrue($user->can('download', $book));
        $this->assertTrue($user->can('read_or_download', $book));
        $this->assertTrue($user->can('manage', $book));
        $this->assertFalse($user->can('attachAward', $book));
        $this->assertFalse($user->can('view_deleted', $book));
        $this->assertFalse($user->can('cancel_parse', $book));

        $attachment = Attachment::factory()->create(['book_id' => $book->id]);

        $file = BookFile::factory()->txt()->create(['book_id' => $book->id]);

        $this->assertTrue($user->can('create_section', $book));
        $this->assertTrue($user->can('update', $section));
        $this->assertTrue($user->can('delete', $section));
        $this->assertTrue($user->can('save_sections_position', $book));
        $this->assertTrue($user->can('move_sections_to_notes', $book));

        $this->assertTrue($user->can('create_attachment', $book));
        $this->assertTrue($user->can('delete', $attachment));
        $this->assertFalse($user->can('setAsCover', $cover));
        $this->assertTrue($user->can('setAsCover', $attachment));
        $this->assertTrue($user->can('remove_cover', $book));

        $book_keyword = BookKeyword::factory()->create(['book_id' => $book->id]);

        $this->assertTrue($user->can('addKeywords', $book));
        $this->assertTrue($user->can('delete', $book_keyword));
        $this->assertTrue($user->can('vote', $book_keyword));
        $this->assertFalse($user->can('viewOnCheck', $book_keyword));

        $this->assertTrue($user->can('addFiles', $book));
        $this->assertTrue($user->can('update', $file));
        $this->assertFalse($user->can('delete', $file));
        $this->assertFalse($user->can('restore', $file));
        $this->assertFalse($user->can('view_on_moderation', $file));
        $this->assertTrue($user->can('set_source_and_make_pages', $file));
    }

    public function testIfVerifiedAuthorAndBookIsPublishedByPublisher()
    {
        $user = User::factory()->with_user_group()->create();
        $user->group->add_comment = true;
        $user->group->book_similar_vote = true;
        $user->group->book_keyword_vote = true;
        $user->push();
        $user->refresh();

        $author = Author::factory()->create();

        $manager = Manager::factory()->create([
            'character' => 'author',
            'user_id' => $user->id,
            'manageable_id' => $author->id
        ]);

        $this->assertEquals('author', $manager->character);
        $this->assertTrue($user->can('manage', $author));

        $book = Book::factory()->create(['is_si' => false]);
        $book->writers()->sync([$author->id]);
        $book->user_vote_count = 1;
        $book->save();

        $section = Section::factory()->create(['book_id' => $book->id]);

        $attachment = Attachment::factory()->create(['book_id' => $book->id]);

        $file = BookFile::factory()->txt()->create(['book_id' => $book->id]);

        $book->refresh();

        $this->assertTrue($user->can('update', $book));
        $this->assertFalse($user->can('addForReview', $book));
        $this->assertFalse($user->can('makeAccepted', $book));
        $this->assertFalse($user->can('addToPrivate', $book));
        $this->assertFalse($user->can('delete', $book));
        $this->assertFalse($user->can('restore', $book));
        $this->assertTrue($user->can('group', $book));
        $this->assertTrue($user->can('ungroup', $book));
        $this->assertTrue($user->can('make_main_in_group', $book));
        $this->assertTrue($user->can('change_access', $book));
        $this->assertTrue($user->can('commentOn', $book));
        $this->assertTrue($user->can('add_similar_book', $book));
        $this->assertTrue($user->can('view_section_list', $book));
        $this->assertFalse($user->can('retry_failed_parse', $book));
        $this->assertFalse($user->can('remove_cover', $book));
        $this->assertFalse($user->can('view_on_moderation', $book));
        $this->assertTrue($user->can('view_group_books', $book));
        $this->assertFalse($user->can('watch_activity_logs', $book));
        $this->assertFalse($user->can('display_technical_information', $book));
        $this->assertTrue($user->can('refresh_counters', $book));
        $this->assertFalse($user->can('open_comments', $book));
        $this->assertFalse($user->can('close_comments', $book));
        $this->assertTrue($user->can('read', $book));
        $this->assertTrue($user->can('view_download_files', $book));
        $this->assertTrue($user->can('download', $book));
        $this->assertTrue($user->can('read_or_download', $book));
        $this->assertTrue($user->can('manage', $book));
        $this->assertFalse($user->can('attachAward', $book));
        $this->assertFalse($user->can('set_as_new_read_online_format', $book));
        $this->assertFalse($user->can('view_deleted', $book));
        $this->assertFalse($user->can('cancel_parse', $book));

        $attachment = Attachment::factory()->create(['book_id' => $book->id]);

        $file = BookFile::factory()->txt()->create(['book_id' => $book->id]);

        $this->assertFalse($user->can('create_section', $book));
        $this->assertFalse($user->can('update', $section));
        $this->assertFalse($user->can('delete', $section));
        $this->assertFalse($user->can('save_sections_position', $book));
        $this->assertFalse($user->can('move_sections_to_notes', $book));

        $this->assertFalse($user->can('create_attachment', $book));
        $this->assertFalse($user->can('delete', $attachment));
        $this->assertFalse($user->can('setAsCover', $attachment));
        $this->assertFalse($user->can('remove_cover', $book));

        $book_keyword = BookKeyword::factory()->create(['book_id' => $book->id]);

        $this->assertTrue($user->can('addKeywords', $book));
        $this->assertTrue($user->can('delete', $book_keyword));
        $this->assertTrue($user->can('vote', $book_keyword));
        $this->assertFalse($user->can('viewOnCheck', $book_keyword));

        $this->assertTrue($user->can('addFiles', $book));
        $this->assertTrue($user->can('update', $file));
        $this->assertFalse($user->can('delete', $file));
        $this->assertFalse($user->can('restore', $file));
        $this->assertFalse($user->can('view_on_moderation', $file));
        $this->assertFalse($user->can('set_source_and_make_pages', $file));
    }

    public function testIfCharacterEditorAndManagerNotCreatorOfBook()
    {
        $user = User::factory()->with_user_group()->create();
        $user->group->add_comment = true;
        $user->group->book_similar_vote = true;
        $user->group->book_keyword_vote = true;
        $user->push();
        $user->refresh();

        $author = Author::factory()->create();

        $manager = Manager::factory()
            ->create([
                'character' => 'editor',
                'user_id' => $user->id,
                'manageable_id' => $author->id
            ]);

        $this->assertEquals('editor', $manager->character);
        $this->assertTrue($user->can('manage', $author));

        $book = Book::factory()->create(['is_si' => false]);
        $book->writers()->sync([$author->id]);

        $section = Section::factory()->create(['book_id' => $book->id]);

        $attachment = Attachment::factory()->create(['book_id' => $book->id]);

        $file = BookFile::factory()->txt()->create(['book_id' => $book->id]);

        $book->refresh();

        $this->assertFalse($user->can('update', $book));
        $this->assertFalse($user->can('addForReview', $book));
        $this->assertFalse($user->can('makeAccepted', $book));
        $this->assertFalse($user->can('addToPrivate', $book));
        $this->assertFalse($user->can('delete', $book));
        $this->assertFalse($user->can('restore', $book));
        $this->assertFalse($user->can('group', $book));
        $this->assertFalse($user->can('ungroup', $book));
        $this->assertTrue($user->can('make_main_in_group', $book));
        $this->assertFalse($user->can('change_access', $book));
        $this->assertTrue($user->can('commentOn', $book));

        $this->assertTrue($user->can('add_similar_book', $book));
        $this->assertTrue($user->can('view_section_list', $book));
        $this->assertFalse($user->can('retry_failed_parse', $book));
        $this->assertFalse($user->can('remove_cover', $book));
        $this->assertFalse($user->can('view_on_moderation', $book));
        $this->assertTrue($user->can('view_group_books', $book));
        $this->assertFalse($user->can('watch_activity_logs', $book));
        $this->assertFalse($user->can('display_technical_information', $book));
        $this->assertTrue($user->can('refresh_counters', $book));
        $this->assertFalse($user->can('open_comments', $book));
        $this->assertFalse($user->can('close_comments', $book));
        $this->assertTrue($user->can('read', $book));
        $this->assertTrue($user->can('view_download_files', $book));
        $this->assertTrue($user->can('download', $book));
        $this->assertTrue($user->can('read_or_download', $book));
        $this->assertFalse($user->can('manage', $book));
        $this->assertFalse($user->can('attachAward', $book));
        $this->assertFalse($user->can('set_as_new_read_online_format', $book));
        $this->assertFalse($user->can('view_deleted', $book));
        $this->assertFalse($user->can('cancel_parse', $book));

        $this->assertFalse($user->can('create_section', $book));
        $this->assertFalse($user->can('save_sections_position', $book));
        $this->assertFalse($user->can('move_sections_to_notes', $book));
        $this->assertFalse($user->can('update', $section));
        $this->assertFalse($user->can('delete', $section));
        $this->assertFalse($user->can('restore', $section));

        $this->assertFalse($user->can('create_attachment', $book));
        $this->assertFalse($user->can('delete', $attachment));
        $this->assertFalse($user->can('restore', $attachment));
        $this->assertFalse($user->can('setAsCover', $attachment));

        $this->assertFalse($user->can('addFiles', $book));
        $this->assertTrue($user->can('update', $file));
        $this->assertFalse($user->can('delete', $file));
        $this->assertFalse($user->can('restore', $file));
        $this->assertFalse($user->can('view_on_moderation', $file));
        $this->assertFalse($user->can('set_source_and_make_pages', $file));

        $book_keyword = BookKeyword::factory()->create(['book_id' => $book->id]);

        $this->assertTrue($user->can('addKeywords', $book));
        $this->assertTrue($user->can('delete', $book_keyword));
        $this->assertTrue($user->can('vote', $book_keyword));
        $this->assertFalse($user->can('viewOnCheck', $book_keyword));
    }

    public function testCanEditBookIfCreatorAndSi()
    {
        $author = Author::factory()->with_book()->with_author_manager()->create();

        $manager = $author->managers->first();
        $user = $manager->user;

        $book = $author->books->first();
        $book->is_si = true;
        $book->create_user()->associate($user);
        $book->save();

        $this->assertTrue($user->can('update', $book));
    }

    public function testCanEditBookIfNotCreatorAndSi()
    {
        $author = Author::factory()->with_book()->with_author_manager()->create();

        $manager = $author->managers->first();
        $user = $manager->user;

        $book = $author->books->first();
        $book->is_si = true;
        $book->save();

        $this->assertTrue($user->can('update', $book));
    }

    public function testCanEditBookIfCreatorAndNotSi()
    {
        $author = Author::factory()->with_book()->with_author_manager()->create();

        $manager = $author->managers->first();
        $user = $manager->user;

        $book = $author->books->first();
        $book->is_si = false;
        $book->create_user()->associate($user);
        $book->save();

        $this->assertTrue($user->can('update', $book));
    }

    public function testCharacterEditorCanEditBookIfCreatorAndSi()
    {
        $author = Author::factory()->with_book()->with_editor_manager()->create();

        $manager = $author->managers->first();
        $user = $manager->user;

        $book = $author->books->first();
        $book->is_si = true;
        $book->pi_pub = null;
        $book->pi_city = null;
        $book->pi_year = null;
        $book->pi_isbn = null;
        $book->save();
        $book->create_user()->associate($user);
        $book->save();

        $this->assertTrue($user->can('update', $book));
    }

    public function testCharacterEditorCantEditBookIfNotCreatorAndSi()
    {
        $author = Author::factory()->with_book()->with_editor_manager()->create();

        $manager = $author->managers->first();
        $user = $manager->user;

        $book = $author->books->first();
        $book->is_si = true;
        $book->save();

        $this->assertFalse($user->can('update', $book));
    }

    public function testCharacterEditorCantEditBookIfCreatorAndNotSi()
    {
        $author = Author::factory()->with_book()->with_editor_manager()->create();

        $manager = $author->managers->first();
        $user = $manager->user;

        $book = $author->books->first();
        $book->is_si = false;
        $book->create_user()->associate($user);
        $book->save();

        $this->assertFalse($user->can('update', $book));
    }

    public function testIfAdmin()
    {
        $user = User::factory()->admin()->create();

        $author = Author::factory()->with_book()->create();

        $book = $author->books->first();
        $book->writers()->sync([$author->id]);

        $section = Section::factory()->create(['book_id' => $book->id]);

        $attachment = Attachment::factory()->create(['book_id' => $book->id]);

        $file = BookFile::factory()->txt()->create();

        $book->refresh();

        $this->assertTrue($user->can('update', $book));
        $this->assertTrue($user->can('delete', $book));
        $this->assertTrue($user->can('group', $book));
        $this->assertTrue($user->can('ungroup', $book));
        $this->assertTrue($user->can('make_main_in_group', $book));
        $this->assertTrue($user->can('change_access', $book));
        $this->assertTrue($user->can('commentOn', $book));
        $this->assertTrue($user->can('add_similar_book', $book));
        $this->assertTrue($user->can('view_section_list', $book));
        $this->assertTrue($user->can('view_on_moderation', $book));
        $this->assertTrue($user->can('view_group_books', $book));
        $this->assertTrue($user->can('watch_activity_logs', $book));
        $this->assertTrue($user->can('display_technical_information', $book));
        $this->assertTrue($user->can('refresh_counters', $book));
        $this->assertTrue($user->can('close_comments', $book));
        $this->assertTrue($user->can('read', $book));
        $this->assertTrue($user->can('view_download_files', $book));
        $this->assertTrue($user->can('download', $book));
        $this->assertTrue($user->can('read_or_download', $book));
        $this->assertTrue($user->can('attachAward', $book));
        $this->assertTrue($user->can('view_deleted', $book));

        $this->assertTrue($user->can('create_section', $book));
        $this->assertTrue($user->can('update', $section));
        $this->assertTrue($user->can('delete', $section));
        $this->assertTrue($user->can('save_sections_position', $book));
        $this->assertTrue($user->can('move_sections_to_notes', $book));

        $this->assertTrue($user->can('create_attachment', $book));
        $this->assertTrue($user->can('delete', $attachment));
        $this->assertTrue($user->can('setAsCover', $attachment));

        $this->assertTrue($user->can('addFiles', $book));
        $this->assertTrue($user->can('update', $file));
        $this->assertTrue($user->can('delete', $file));
        $this->assertTrue($user->can('view_on_moderation', $file));
        $this->assertTrue($user->can('set_source_and_make_pages', $file));

        $book_keyword = BookKeyword::factory()->create(['book_id' => $book->id]);

        $this->assertTrue($user->can('addKeywords', $book));
        $this->assertTrue($user->can('delete', $book_keyword));
        $this->assertTrue($user->can('vote', $book_keyword));
        $this->assertTrue($user->can('viewOnCheck', $book_keyword));
    }

    public function testPolicyIfManager()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $book = Book::factory()->with_translator()->with_writer()->create();

        $book->refresh();

        $this->assertFalse($user->can('manage', $book));
        $this->assertFalse($user2->can('manage', $book));

        $author_manager = Manager::factory()
            ->character_author()
            ->create([
                'user_id' => $user->id,
                'manageable_id' => $book->writers->first()->id,
                'manageable_type' => 'author'
            ]);

        $author_manager->statusSentForReview();
        $author_manager->save();

        $author_manager2 = Manager::factory()
            ->character_author()
            ->create([
                'user_id' => $user2->id,
                'manageable_id' => $book->translators->first()->id,
                'manageable_type' => 'author'
            ]);

        $author_manager2->statusSentForReview();
        $author_manager2->save();

        $book->refresh();

        $this->assertFalse($user->can('manage', $book));
        $this->assertFalse($user2->can('manage', $book));

        $author_manager->statusAccepted();
        $author_manager->save();
        $author_manager2->statusAccepted();
        $author_manager2->save();

        $book->refresh();

        $this->assertTrue($user->can('manage', $book));
        $this->assertTrue($user2->can('manage', $book));

        $author_manager->delete();
        $author_manager2->delete();

        $book->refresh();

        $this->assertFalse($user->can('manage', $book));
        $this->assertFalse($user2->can('manage', $book));
    }

    public function testAuthorPolicy()
    {
        $author = Author::factory()->with_author_manager()->with_book_for_sale()->create();

        $manager = $author->managers->first();
        $book = $author->books->first();
        $user = $manager->user;

        $this->assertTrue($user->can('author', $book));

        $user = User::factory()->create();

        $this->assertFalse($user->can('author', $book));

        $author = Author::factory()->with_author_manager()->with_book_for_sale()->create();

        $manager = $author->managers->first();
        $user = $manager->user;

        $this->assertFalse($user->can('author', $book));
    }
}
