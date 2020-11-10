<?php

use App\Enums\TopicLabelEnum;
use App\Post;
use App\Topic;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Topic::class, function (Faker $faker) {

    return [
        'name' => $faker->realText(70) . ' ' . Str::random(20),
        'description' => $faker->realText(100),
        'create_user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'forum_id' => function () {
            return factory(App\Forum::class)->create()->id;
        },
    ];
});

$factory->afterMakingState(App\Topic::class, 'idea_implemented', function (Topic $topic, $faker) {
    $topic->label = TopicLabelEnum::IdeaImplemented;
});

$factory->afterMakingState(App\Topic::class, 'idea_on_review', function (Topic $topic, $faker) {
    $topic->label = TopicLabelEnum::IdeaOnReview;
});

$factory->afterMakingState(App\Topic::class, 'idea_rejected', function (Topic $topic, $faker) {
    $topic->label = TopicLabelEnum::IdeaRejected;
});

$factory->afterMakingState(App\Topic::class, 'idea_in_progress', function (Topic $topic, $faker) {
    $topic->label = TopicLabelEnum::IdeaInProgress;
});

$factory->afterMakingState(App\Topic::class, 'archived', function (Topic $topic, $faker) {
    $topic->archive();
});

$factory->afterMakingState(App\Topic::class, 'closed', function (Topic $topic, $faker) {
    $topic->close();
});

$factory->afterCreatingState(App\Topic::class, 'with_post', function (Topic $topic, $faker) {
    $post = factory(Post::class)->make();
    $topic->posts()->save($post);
});

$factory->afterCreatingState(App\Topic::class, 'with_fixed_post', function (Topic $topic, $faker) {
    $post = factory(Post::class)->make();
    $topic->posts()->save($post);
    $post->fix();
});

