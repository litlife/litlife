<?php

use App\Enums\StatusEnum;
use App\Enums\VariablesEnum;
use App\Forum;
use App\ForumGroup;
use App\Post;
use App\Variable;
use Faker\Generator as Faker;

$factory->define(App\Post::class, function (Faker $faker) {

    $text = $faker->realText(200);

    return [
        'create_user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'html_text' => $text,
        'bb_text' => $text,
        'topic_id' => function () {
            return factory(App\Topic::class)->create()->id;
        },
        'status' => StatusEnum::Accepted,
    ];
});

$factory->afterMakingState(App\Post::class, 'with_forum_group', function (Post $post, $faker) {

    $group = factory(ForumGroup::class)
        ->create();

    $topic = $post->topic;
    $forum = $topic->forum;

    $forum->group()->associate($group);
    $forum->save();
});

$factory->afterMakingState(App\Post::class, 'create_user_with_achievement', function (Post $post, $faker) {

    $post->create_user_id = factory(App\User::class)
        ->states('with_achievement')
        ->create()->id;
});

$factory->afterCreatingState(App\Post::class, 'sent_for_review', function (Post $post, $faker) {
    $post->statusSentForReview();
    $post->save();
});

$factory->state(App\Post::class, 'idea_forum_posts', function ($faker) {

    $value = Variable::where('name', VariablesEnum::IdeaForum)->firstOrFail()->value;

    $forum = Forum::findOrFail($value);

    $topic = factory(App\Topic::class)
        ->states('idea_on_review')
        ->create([
            'forum_id' => $forum->id
        ]);

    return [
        'forum_id' => function () use ($forum) {
            return $forum->id;
        },
        'topic_id' => function () use ($topic) {
            return $topic->id;
        },
    ];
});

$factory->afterCreatingState(App\Post::class, 'idea_forum_posts', function (Post $post, $faker) {
    $post->fix();
});

$factory->afterCreatingState(App\Post::class, 'fixed', function (Post $post, $faker) {
    $post->fix();
    $post->refresh();
});