<?php

namespace App;

use App\Library\BBCode\BBCode;
use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Stevebauman\Purify\Facades\Purify;

/**
 * App\UserNote
 *
 * @property int $id
 * @property int $create_user_id
 * @property string $text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string $bb_text
 * @property bool $external_images_downloaded
 * @property-read \App\User $create_user
 * @property-write mixed $b_b_text
 * @method static \Illuminate\Database\Eloquent\Builder|UserNote any()
 * @method static \Illuminate\Database\Eloquent\Builder|UserNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserNote newQuery()
 * @method static Builder|UserNote onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|UserNote query()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereBbText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereExternalImagesDownloaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserNote whereUpdatedAt($value)
 * @method static Builder|UserNote withTrashed()
 * @method static Builder|UserNote withoutTrashed()
 * @mixin Eloquent
 */
class UserNote extends Model
{
    use SoftDeletes;
    use UserCreate;

    protected $fillable = [
        'bb_text'
    ];

    public function scopeAny($query)
    {
        return $query->withTrashed();
    }

    public function setBBTextAttribute($bb)
    {
        $bb = mb_substr($bb, 0, 1000000);
        $bb = trim(replaceAsc194toAsc32($bb));
        $bb = removeJsAdCode($bb);

        $html = (new BBCode)->toHtml($bb);

        if (trim($html) == '') {
            $html = null;
        }

        $this->attributes['bb_text'] = $bb;
        $this->attributes['text'] = $html;
        $this->attributes['external_images_downloaded'] = false;
    }

    public function setTextAttribute($value)
    {
        $value = trim(replaceAsc194toAsc32($value));
        $value = removeJsAdCode($value);
        $value = preg_replace("/<br(\ *)\/?>(\ *)<br(\ *)\/?>/iu", "\n\n", $value);
        $this->attributes['text'] = @Purify::clean($value);
        $this->attributes['external_images_downloaded'] = false;
    }
}
