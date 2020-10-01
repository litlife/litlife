<?php

namespace Tests\Feature\Complain;

use App\Blog;
use App\Book;
use App\Comment;
use App\Complain;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class ComplainIndexTest extends TestCase
{
	public function testIfCommentDeleted()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$comment = factory(Comment::class)
			->create();

		$complain = factory(Complain::class)
			->create([
				'complainable_type' => 'comment',
				'complainable_id' => $comment->id
			]);

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($comment->text)
			->assertSeeText($complain->text);

		$comment->delete();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($comment->text)
			->assertSeeText($complain->text);

		$comment->forceDelete();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($complain->text);
	}

	public function testIfPostDeleted()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$complain = factory(Complain::class)
			->states('post')
			->create();

		$post = $complain->complainable;

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($post->text)
			->assertSeeText($complain->text);

		$post->delete();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($post->text)
			->assertSeeText($complain->text);

		$post->forceDelete();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($complain->text);
	}

	public function testIfWallPostDeleted()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$complain = factory(Complain::class)
			->states('wall_post')
			->create();

		$wall_post = $complain->complainable;

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($wall_post->text)
			->assertSeeText($complain->text);

		$wall_post->delete();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($wall_post->text)
			->assertSeeText($complain->text);

		$wall_post->forceDelete();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($complain->text);
	}

	public function testComplainForBook()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$complain = factory(Complain::class)
			->states('book')
			->create();

		$this->assertInstanceOf(Book::class, $complain->complainable);

		$title = Str::random(10);

		$complain->complainable->title = $title;
		$complain->push();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk()
			->assertSeeText($title);
	}


	public function testIfWallPostCreatorIsDeleted()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$complain = factory(Complain::class)
			->states('wall_post')
			->create();

		$this->assertInstanceOf(Blog::class, $complain->complainable);

		$wall_post = $complain->complainable;

		$wall_post->owner->delete();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk();
	}

	public function testIfWallPostCreateUserIsDeleted()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$complain = factory(Complain::class)
			->states('wall_post')
			->create();

		$this->assertInstanceOf(Blog::class, $complain->complainable);

		$wall_post = $complain->complainable;

		$wall_post->create_user->delete();

		$this->actingAs($admin)
			->get(route('complaints.index'))
			->assertOk();
	}
}
