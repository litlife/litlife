<?php

namespace App;

use App\Enums\UserRelationType;
use App\Model as Model;
use App\Traits\Cachable;
use Eloquent;
use GeneaLabs\LaravelModelCaching\CachedBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\UserSetting
 *
 * @property int $user_id
 * @property string|null $bookmark_folder_order
 * @property string|null $genre_blacklist
 * @property int|null $blog_top_record
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool $login_with_id Можно ли использовать в качестве логина id
 * @property int $font_size_px
 * @property int|null $font_family
 * @property mixed $email_delivery
 * @property mixed $user_access
 * @property-read \App\Blog|null $top_blog_record
 * @property-read \App\User $user
 * @method static Builder|UserSetting disableCache()
 * @method static CachedBuilder|UserSetting newModelQuery()
 * @method static CachedBuilder|UserSetting newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static CachedBuilder|UserSetting query()
 * @method static Builder|Model void()
 * @method static Builder|UserSetting whereBlogTopRecord($value)
 * @method static Builder|UserSetting whereBookmarkFolderOrder($value)
 * @method static Builder|UserSetting whereCreatedAt($value)
 * @method static Builder|UserSetting whereFontFamily($value)
 * @method static Builder|UserSetting whereFontSizePx($value)
 * @method static Builder|UserSetting whereGenreBlacklist($value)
 * @method static Builder|UserSetting whereLoginWithId($value)
 * @method static Builder|UserSetting whereUpdatedAt($value)
 * @method static Builder|UserSetting whereUserId($value)
 * @method static Builder|UserSetting withCacheCooldownSeconds($seconds = null)
 * @mixin Eloquent
 * @method static CachedBuilder|UserSetting all($columns = [])
 * @method static CachedBuilder|UserSetting avg($column)
 * @method static CachedBuilder|UserSetting cache(array $tags = [])
 * @method static CachedBuilder|UserSetting cachedValue(array $arguments, string $cacheKey)
 * @method static CachedBuilder|UserSetting count($columns = '*')
 * @method static CachedBuilder|UserSetting disableModelCaching()
 * @method static CachedBuilder|UserSetting flushCache(array $tags = [])
 * @method static CachedBuilder|UserSetting getModelCacheCooldown(\Illuminate\Database\Eloquent\Model $instance)
 * @method static CachedBuilder|UserSetting inRandomOrder($seed = '')
 * @method static CachedBuilder|UserSetting insert(array $values)
 * @method static CachedBuilder|UserSetting isCachable()
 * @method static CachedBuilder|UserSetting max($column)
 * @method static CachedBuilder|UserSetting min($column)
 * @method static CachedBuilder|UserSetting sum($column)
 * @method static CachedBuilder|UserSetting truncate()
 */
class UserSetting extends Model
{
    use Cachable;

    public $permissionCollection;

    public $emailDeliveryCollection;

    protected $primaryKey = 'user_id';

    protected $attributes =
        [
            'bookmark_folder_order' => '',
            'blog_top_record' => null,
            'login_with_id' => false,
            'font_size_px' => 15,
            'font_family' => null
        ];

    protected $fillable = [
        'user_id',
        'login_with_id',
        'font_size_px',
        'font_family'
    ];

    protected $casts = [
        'login_with_id' => 'boolean',
        'permissions_to_act' => 'array'
    ];

    function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $choices = collect([
            'everyone',
            'friends',
            'me',
            'friends_and_subscribers'
        ]);

        $this->permissionCollection = collect([
            [
                'name' => 'write_on_the_wall',
                'default' => 'friends',
                'choices' => $choices
            ],
            [
                'name' => 'comment_on_the_wall',
                'default' => 'friends',
                'choices' => $choices
            ],
            [
                'name' => 'write_private_messages',
                'default' => 'everyone',
                'choices' => collect([
                    'everyone',
                    'friends',
                    'friends_and_subscribers'
                ])
            ],
        ]);

        $this->emailDeliveryCollection = collect([
            [
                'name' => 'News',
                'default' => true
            ],
            [
                'name' => 'OnMessageComing',
                'default' => true
            ],
            [
                'name' => 'OnForumAnswerComing',
                'default' => true
            ],
            [
                'name' => 'OnNewMessageOnWall',
                'default' => true
            ],
            [
                'name' => 'OnCommentAnswerComing',
                'default' => true
            ],
            [
                'name' => 'OnBlogMessageAnswerComing',
                'default' => true
            ]
        ]);
    }

    public function top_blog_record()
    {
        //return $this->belongsToMany('App\Blog', 'user_settings', 'id', 'blog_top_record');
        return $this->hasOne('App\Blog', "id", "blog_top_record");
    }

    public function user()
    {
        return $this->belongsTo('App\User', "user_id", "id");
    }

    public function setUserAccessAttribute($value)
    {
        $this->attributes['user_access'] = serialize($value);
    }

    public function userAccessCheck($user, $name)
    {
        $choice = $this->getPermission($name)->value;

        //dd($this->user);

        if ($this->user->id == $user->id) {
            return true;
        }

        if ($choice == 'everyone') {
            return true;
        } elseif ($choice == 'me') {
            if ($this->user->id == $user->id) {
                return true;
            }
        } elseif ($choice == 'friends') {
            $relation = $this->user->relationOnUser($user);

            if ((isset($relation->status)) and ($relation->status == UserRelationType::Friend)) {
                return true;
            }
        } elseif ($choice == 'friends_and_subscribers') {
            $relation = $this->user->relationOnUser($user);

            if ((isset($relation->status)) and (in_array($relation->status, [UserRelationType::Friend, UserRelationType::Subscriber]))) {
                return true;
            }
        }

        return false;
    }

    public function getPermission($name)
    {
        $item = $this->permissionCollection
            ->where('name', $name)
            ->first();

        $name = $item['name'];

        if (is_array($this->permissions_to_act)) {
            if (array_key_exists($name, $this->permissions_to_act)) {
                $item['original'] = $this->permissions_to_act[$name];
            }
        }

        if (empty($item['original'])) {
            $item['value'] = $item['default'];
        } else {
            $item['value'] = $item['original'];
        }

        return (object)$item;
    }

    public function setEmailDeliveryAttribute($value)
    {
        $this->attributes['email_delivery'] = serialize($value);
    }

    public function getEmailDeliveryAttribute($value)
    {
        return unserialize($value);
    }

    public function setBookmarkFolderOrderAttribute($value)
    {
        $this->attributes['bookmark_folder_order'] = implode(',', $value);
    }

    function getBookmarkFolderOrderAttribute($value)
    {
        return array_filter(explode(',', $value));
    }

    public function getEmailDelivery($name)
    {
        $item = $this->emailDeliveryCollection->where('name', $name)->first();

        if (is_array($this->email_delivery)) {
            if (array_key_exists($item['name'], $this->email_delivery)) {
                $item['original'] = (bool)$this->email_delivery[$item['name']];
            }
        }

        if (isset($item['original'])) {
            $item['value'] = $item['original'];
        } else {
            $item['value'] = $item['default'];
        }

        return (object)$item;
    }

    function getUserAccessAttribute($value)
    {
        $user_accesses = unserialize($value);

        $choices = [
            'everyone',
            'friends',
            'me',
            'friends_and_subscribers'
        ];

        if (isset($user_accesses[1])) {
            $user_accesses['write_on_the_wall'] = $choices[($user_accesses[1] - 1)];
            unset($user_accesses[1]);
        }

        if (isset($user_accesses[2])) {
            $user_accesses['comment_on_the_wall'] = $choices[($user_accesses[2] - 1)];
            unset($user_accesses[2]);
        }

        if (isset($user_accesses[3])) {
            $user_accesses['write_private_messages'] = $choices[($user_accesses[3] - 1)];
            unset($user_accesses[3]);
        }

        $default_user_accesses = [
            'write_on_the_wall' => 'friends',
            'comment_on_the_wall' => 'friends',
            'write_private_messages' => 'me'
        ];

        foreach ($default_user_accesses as $name => $access) {
            if (!isset($user_accesses[$name])) {
                $user_accesses[$name] = $access;
            } else {
                $user_accesses[$name] = $user_accesses[$name];
            }
        }

        return (object)$user_accesses;
    }

    public function setFontFamilyAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['font_family'] = null;
        } else {
            foreach (config('litlife.available_fonts') as $index => $font) {
                if ($value == $font) {
                    $this->attributes['font_family'] = $index;
                }
            }
        }
    }

    public function getFontFamilyAttribute($value)
    {
        foreach (config('litlife.available_fonts') as $index => $font) {
            if ($value == $index) {
                return $font;
            }
        }
        return null;
    }

    public function isLoginWithIdEnable()
    {
        return (boolean)$this->login_with_id;
    }

    public function loginWithIdEnable()
    {
        $this->login_with_id = true;
    }

    public function loginWithIdDisable()
    {
        $this->login_with_id = false;
    }

    public function bookSearchSettings()
    {

    }
}
