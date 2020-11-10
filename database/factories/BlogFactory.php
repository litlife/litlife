<?php

use App\Blog;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Blog::class, function (Faker $faker) {

    $text = Str::random(10) . ' ' . $faker->realText(200);

    return [
        'blog_user_id' => function () {
            return factory(App\User::class)->states('with_user_permissions')->create()->id;
        },
        'create_user_id' => function () {
            return factory(App\User::class)->states('with_user_permissions')->create()->id;
        },
        'text' => $text,
        'bb_text' => $text,
        'tree' => null,
        'display_on_home_page' => true
    ];
});

$factory->afterMakingState(App\Blog::class, 'fixed', function (Blog $blog, $faker) {
    $blog->blog_user_id = $blog->create_user_id;
});

$factory->afterCreatingState(App\Blog::class, 'fixed', function (Blog $blog, $faker) {
    $blog->fix();
});

$factory->afterCreatingState(App\Blog::class, 'sent_for_review', function (Blog $blog, $faker) {
    $blog->statusSentForReview();
    $blog->save();
});

