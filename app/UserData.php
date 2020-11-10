<?php

namespace App;

use App\Model as Model;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * App\UserData
 *
 * @property int $user_id
 * @property string|null $favorite_authors
 * @property string|null $favorite_genres
 * @property string|null $favorite_music
 * @property string|null $i_love
 * @property string|null $i_hate
 * @property string|null $about_self
 * @property string|null $favorite_quote
 * @property int|null $blog_record_comment_count
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $password_reset_count
 * @property string|null $last_time_password_is_reset
 * @property \Illuminate\Support\Carbon|null $last_news_view_at
 * @property int $created_books_count
 * @property int $created_authors_count
 * @property int $created_sequences_count
 * @property \Illuminate\Support\Carbon|null $favorite_authors_books_latest_viewed_at
 * @property int $books_purchased_count Количество книг купленных пользователем
 * @property int|null $refer_users_count Количество привлеченных пользователей
 * @property int|null $favorite_collections_count Количество избранных подборок
 * @property int|null $created_collections_count Количество созданных подборок
 * @property bool $invitation_to_take_survey_has_been_sent
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserData newQuery()
 * @method static Builder|UserData onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|UserData query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereAboutSelf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereBlogRecordCommentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereBooksPurchasedCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereCreatedAuthorsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereCreatedBooksCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereCreatedCollectionsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereCreatedSequencesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereFavoriteAuthors($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereFavoriteAuthorsBooksLatestViewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereFavoriteCollectionsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereFavoriteGenres($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereFavoriteMusic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereFavoriteQuote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereIHate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereILove($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereInvitationToTakeSurveyHasBeenSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereLastNewsViewAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereLastTimePasswordIsReset($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData wherePasswordResetCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereReferUsersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserData whereUserId($value)
 * @method static Builder|UserData withTrashed()
 * @method static Builder|UserData withoutTrashed()
 * @mixin Eloquent
 */
class UserData extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'user_id';
    protected $table = 'user_datas';

    protected $fillable = [
        'favorite_authors',
        'favorite_genres',
        'favorite_music',
        'i_love',
        'i_hate',
        'about_self',
        'favorite_quote'
    ];

    protected $attributes =
        [
            'favorite_authors' => '',
            'favorite_genres' => '',
            'favorite_music' => '',
            'i_love' => '',
            'i_hate' => '',
            'about_self' => '',
            'favorite_quote' => '',
            'blog_record_comment_count' => 0,
            'created_books_count' => 0,
            'created_authors_count' => 0,
            'created_sequences_count' => 0,
            'books_purchased_count' => 0
        ];

    protected $dates = [
        'last_news_view_at',
        'favorite_authors_books_latest_viewed_at'
    ];

    public function getFavoriteAuthorsAttribute($value)
    {
        return strip_tags($value);
    }

    public function setFavoriteAuthorsAttribute($value)
    {
        $value = strip_tags($value);

        $this->attributes['favorite_authors'] = $value;
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getFavoriteAuthorsBooksLatestViewedAtAttribute($value)
    {
        if (empty($value)) {
            /*
            if (empty($this->user->last_activity_at))
                $value = $this->user->created_at;
            else
                $value = $this->user->last_activity_at;
            */
            $value = Carbon::create(2019, 05, 05, 0, 0, 0);
        }

        return $value;
    }

    public function getReferUsersCountAttribute($value)
    {
        return intval($value);
    }

    public function setReferUsersCountAttribute($value)
    {
        if (empty($value)) {
            $value = null;
        }

        $this->attributes['refer_users_count'] = $value;
    }

    public function getFavoriteCollectionsCountAttribute($value)
    {
        return intval($value);
    }

    public function setFavoriteCollectionsCountAttribute($value)
    {
        if (empty($value)) {
            $value = null;
        }

        $this->attributes['favorite_collections_count'] = $value;
    }

    public function getCreatedCollectionsCountAttribute($value)
    {
        return intval($value);
    }

    public function setCreatedCollectionsCountAttribute($value)
    {
        if (empty($value)) {
            $value = null;
        }

        $this->attributes['created_collections_count'] = $value;
    }
}
