<?php

namespace App;

use App\Enums\CacheTags;
use App\Enums\TopicLabelEnum;
use App\Model as Model;
use App\Traits\UserCreate;
use Cache;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Topic
 *
 * @property int $id
 * @property bool $closed
 * @property int $forum_id
 * @property string $name
 * @property string|null $description
 * @property int $create_user_id
 * @property int $post_count
 * @property int $view_count
 * @property int|null $last_post_id
 * @property bool $post_desc
 * @property int $main_priority
 * @property int|null $top_post_id
 * @property int $forum_priority
 * @property bool $hide_from_main_page
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $user_edited_at Время когда пользователь отредактировал
 * @property string|null $last_post_created_at
 * @property bool $archived
 * @property int|null $label
 * @property-read \App\UserTopicSubscription|null $auth_user_subscription
 * @property-read \App\User $create_user
 * @property-read \App\Forum $forum
 * @property-read \App\Post|null $last_post
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Post[] $posts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Post[] $postsOrderedBySetting
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $subscribed_users
 * @property-read \App\Post|null $top_post
 * @property-read \App\UserTopicSubscription|null $user_subscriptions
 * @method static \Illuminate\Database\Eloquent\Builder|Topic any()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic archived()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic closed()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic dontShowOnMainPage()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic fulltextSearch($searchText)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic newQuery()
 * @method static Builder|Topic onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic opened()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic orderByLastPostAscNullsFirst()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic orderByLastPostDescNullsLast()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic orderByLastPostNullsLast()
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Topic orderForIdeaForum()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic public()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic query()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic trgmSearch($searchText)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic unarchived()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic void()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereForumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereForumPriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereHideFromMainPage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereLastPostCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereLastPostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereMainPriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic wherePostCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic wherePostDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereTopPostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereUserEditedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Topic whereViewCount($value)
 * @method static Builder|Topic withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic withUserAccessToForums()
 * @method static Builder|Topic withoutTrashed()
 * @mixin Eloquent
 */
class Topic extends Model
{
    use SoftDeletes;
    use UserCreate;

    protected $fillable = [
        'name',
        'description',
        'post_desc',
        'main_priority',
        'forum_priority',
        'hide_from_main_page',
        'label'
    ];

    static function cachedLatestTopics()
    {
        return Cache::get(CacheTags::LatestTopicsQuery);
    }

    static function refreshLatestTopics()
    {
        $settings = Variable::where('name', 'settings')->first();

        $topics = Topic::with('last_post.create_user', 'forum')
            ->when(isset($settings->value['hide_from_main_page_forums']), function ($query) use ($settings) {
                return $query->whereNotIn('topics.forum_id', $settings->value['hide_from_main_page_forums']);
            })
            ->select('topics.*')
            ->public()
            ->dontShowOnMainPage()
            ->orderByLastPostNullsLast()
            ->limit(3)
            ->get();

        Cache::forever(CacheTags::LatestTopicsQuery, $topics);

        return $topics;
    }

    public function scopeAny($query)
    {
        return $query->withTrashed();
    }

    public function postsOrderedBySetting()
    {
        return $this->hasMany('App\Post', 'topic_id')
            ->orderBy('id', $this->post_desc ? 'desc' : 'asc');
    }

    public function forum()
    {
        return $this->belongsTo('App\Forum')->any();
    }

    public function last_post()
    {
        return $this->hasOne('App\Post', 'id', 'last_post_id')
            ->with('create_user');
    }

    public function top_post()
    {
        return $this->hasOne('App\Post', 'id', 'top_post_id')
            ->with('create_user');
    }

    public function create_user()
    {
        return $this->belongsTo('App\User', $this->getCreateUserIdColumn(), 'id')
            ->with("avatar");
    }

    public function scopeFulltextSearch($query, $searchText)
    {
        $Ar = preg_split("/[\s,[:punct:]]+/", $searchText, 0, PREG_SPLIT_NO_EMPTY);

        $s = '';

        if ($Ar) {
            $s = "to_tsvector('english', \"" . $this->getTable() . "\".\"name\" )  ";
            $s .= " @@ to_tsquery('english', quote_literal(quote_literal(?)))";

            return $query->whereRaw($s, [implode('+', $Ar)]);
        }
    }

    public function scopeTrgmSearch($query, $searchText)
    {
        $Ar = preg_split("/[\s,[:punct:]]+/", $searchText, 0, PREG_SPLIT_NO_EMPTY);

        $s = '';

        if ($Ar) {
            /*
            $query->whereRaw("to_tsvector('english', \"name\") @@ to_tsquery('english', quote_literal(quote_literal(?)))", [implode('&', $Ar)])
            */
            $s = "to_tsvector('english', \"name\") ";
            $s .= " @@ to_tsquery(quote_literal(quote_literal(?)) || ':*')";
            return $query->whereRaw($s, implode('+', $Ar));
        }
        return $query;
    }

    public function scopeVoid($query)
    {
        return $query;
    }

    public function isClosed()
    {
        return (bool)$this->closed;
    }

    public function isOpened()
    {
        return (bool)!$this->closed;
    }

    public function open()
    {
        $this->closed = false;
    }

    public function close()
    {
        $this->closed = true;
    }

    public function scopeOpened($query)
    {
        return $query->where('closed', true);
    }

    public function scopeClosed($query)
    {
        return $query->where('closed', false);
    }

    public function scopeOrderByLastPostNullsLast($query)
    {
        //return $query->orderByRaw('"last_post_created_at" desc nulls last');
        return $query->orderByWithNulls('last_post_created_at', 'desc', 'last');
        //return $query->orderByRaw('"last_post_created_at" ? nulls ?', ['desc', 'last']);
    }

    public function scopeOrderByLastPostDescNullsLast($query)
    {
        //return $query->orderByRaw('"last_post_created_at" desc nulls last');
        return $query->orderByWithNulls('last_post_created_at', 'desc', 'last');
        //return $query->orderByRaw('"last_post_created_at" ? nulls ?', ['desc', 'last']);
    }

    public function scopeOrderByLastPostAscNullsFirst($query)
    {
        //return $query->orderByRaw('"last_post_created_at" desc nulls last');
        return $query->orderByWithNulls('last_post_created_at', 'asc', 'first');
        //return $query->orderByRaw('"last_post_created_at" ? nulls ?', ['desc', 'last']);
    }

    public function archive()
    {
        $this->archived = true;
    }

    public function unarchive()
    {
        $this->archived = false;
    }

    public function isArchived()
    {
        return (bool)$this->archived;
    }

    public function scopeArchived($query)
    {
        return $query->where('archived', true);
    }

    public function scopeUnarchived($query)
    {
        return $query->where('archived', false);
    }

    public function scopeWithUserAccessToForums($query)
    {
        return $query->join('forums', 'forums.id', '=', 'topics.forum_id')
            ->leftJoin('users_access_to_forums', function ($join) {
                $join->on('users_access_to_forums.forum_id', '=', 'topics.forum_id')
                    ->where('users_access_to_forums.user_id', auth()->id());
            })->where(function ($query) {
                $query->where('forums.private', false)
                    ->orWhere(function ($query) {
                        $query->where('forums.private', true)
                            ->whereNotNull('users_access_to_forums.user_id');
                    });
            });
    }

    public function scopePublic($query)
    {
        return $query->join('forums', 'forums.id', '=', 'topics.forum_id')
            ->where(function ($query) {
                $query->where('forums.private', false);
            });
    }

    public function scopeDontShowOnMainPage($query)
    {
        return $query->where('hide_from_main_page', false);
    }

    public function scopeOrderForIdeaForum($query)
    {
        $bindings = [TopicLabelEnum::IdeaInProgress, TopicLabelEnum::IdeaOnReview];

        $qs = 'CASE ';
        $qs .= 'WHEN "label" = ? THEN 1 ';
        $qs .= 'WHEN "label" = ? THEN 1 ';
        $qs .= 'ELSE 2 ';
        $qs .= 'END';

        // dd($bindings);

        return $query->orderByRaw($qs, $bindings)
            ->orderByRaw('posts.like_count desc nulls last');
    }

    public function subscribed_users()
    {
        return $this->belongsToMany('App\User', 'user_topic_subscriptions')
            ->withTimestamps();
    }

    public function user_subscriptions()
    {
        return $this->hasOne('App\UserTopicSubscription');
    }

    public function auth_user_subscription()
    {
        return $this->hasOne('App\UserTopicSubscription')
            ->where('user_id', auth()->id());
    }

    /**
     * Обновляет количество постов в теме и последний пост
     */
    public function updateCountOfPostsAndLastPost()
    {
        $this->postsCountRefresh();
        $this->lastPostRefresh();
        $this->save();
    }

    public function postsCountRefresh()
    {
        $this->post_count = $this->posts()
            ->count();
    }

    public function posts()
    {
        return $this->hasMany('App\Post', 'topic_id');
    }

    public function lastPostRefresh()
    {
        $post = $this->posts()
            ->latestWithId()
            ->first();

        if (!empty($post)) {
            $this->last_post_id = $post->id;
            $this->last_post_created_at = $post->created_at;
        } else {
            $this->last_post_id = null;
            $this->last_post_created_at = null;
        }
    }
}
