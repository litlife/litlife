<?php

namespace Tests\Feature\Search;

use App\SearchQueriesLog;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchTest extends TestCase
{
    public function testResultNotingFound()
    {
        $this->get(route('search', ['query' => Str::random(10)]))
            ->assertOk()
            ->assertSeeText(__('search.nothing_found'))
            ->assertViewHas('books_count', 0)
            ->assertViewHas('authors_count', 0)
            ->assertViewHas('sequences_count', 0)
            ->assertViewHas('users_count', 0)
            ->assertViewHas('collections_count', 0);
    }

    public function testResultMoreUrls()
    {
        $query = Str::random(10);

        $this->get(route('search', ['query' => $query]))
            ->assertOk()
            ->assertViewHas('books_url', route('books', [
                'search' => $query, 'order' => 'rating_avg_down',
                'paid_access' => 'any', 'read_access' => 'any', 'download_access' => 'any'
            ]))
            ->assertViewHas('authors_url', route('authors', ['search' => $query, 'order' => 'rating']))
            ->assertViewHas('sequences_url', route('sequences', ['search' => $query, 'order' => 'book_count_desc']))
            ->assertViewHas('users_url', route('users', ['search' => $query]))
            ->assertViewHas('collections_url', route('collections.index', ['search' => $query, 'order' => 'likes_count_desc']));
    }

    public function testResultAjax()
    {
        $query = Str::random(10);

        $this->get(route('search', ['query' => $query]), ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk();
    }

    public function testSearchGoogleIsOk()
    {
        $this->get(route('search.google'))
            ->assertOk();
    }

    public function testMinimumNumberLettersAndNumbers()
    {
        config(['litlife.minimum_number_of_letters_and_numbers' => 5]);

        $query = Str::random(3).'.,%$^$^&##$%';

        $this->get(route('search', ['query' => $query]))
            ->assertOk()
            ->assertSeeText(trans_choice('search.minimum_length_of_the_search_string',
                config('litlife.minimum_number_of_letters_and_numbers'),
                ['characters_count' => config('litlife.minimum_number_of_letters_and_numbers')]
            ));
    }

    public function testLogCreatedAuthUser()
    {
        $user = User::factory()->create();

        $query = Str::random(10);

        $this->actingAs($user)
            ->get(route('search', ['query' => $query]))
            ->assertOk();

        $searchQueryLog = $user->searchQueries()->first();

        $this->assertNotNull($searchQueryLog);
        $this->assertEquals($user->id, $searchQueryLog->user_id);
        $this->assertEquals($query, $searchQueryLog->query_text);
    }

    public function testLogCreatedGuest()
    {
        $query = Str::random(10);

        $this->get(route('search', ['query' => $query]))
            ->assertOk();

        $searchQueryLog = SearchQueriesLog::where('query_text', $query)->first();

        $this->assertNotNull($searchQueryLog);
        $this->assertNull($searchQueryLog->user_id);
        $this->assertEquals($query, $searchQueryLog->query_text);
    }

    public function testMinLength()
    {
        $query = 't';

        $this->get(route('search', ['query' => $query]))
            ->assertOk()
            ->assertSeeText(trans_choice('search.minimum_length_of_the_search_string', config('litlife.minimum_number_of_letters_and_numbers'),
                ['characters_count' => config('litlife.minimum_number_of_letters_and_numbers')]));
    }

    public function testMaxLength()
    {
        $query = Str::random(300);

        $this->get(route('search', ['query' => $query]))
            ->assertOk();
    }

}