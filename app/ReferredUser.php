<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\ReferredUser
 *
 * @property int $id
 * @property int $referred_by_user_id
 * @property int $referred_user_id
 * @property int $comission_buy_book
 * @property int $comission_sell_book
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $referred_by_user
 * @property-read User $referred_user
 * @method static Builder|ReferredUser newModelQuery()
 * @method static Builder|ReferredUser newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|ReferredUser query()
 * @method static Builder|Model void()
 * @method static Builder|ReferredUser whereComissionBuyBook($value)
 * @method static Builder|ReferredUser whereComissionSellBook($value)
 * @method static Builder|ReferredUser whereCreatedAt($value)
 * @method static Builder|ReferredUser whereId($value)
 * @method static Builder|ReferredUser whereReferredByUserId($value)
 * @method static Builder|ReferredUser whereReferredUserId($value)
 * @method static Builder|ReferredUser whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ReferredUser extends Model
{
    protected $fillable = [
        'referred_by_user_id',
        'referred_user_id'
    ];

    public static function boot()
    {
        static::Creating(function ($model) {

            if (empty($model->comission_buy_book)) {
                $model->comission_buy_book = config('litlife.comission_from_refrence_buyer');
            }

            if (empty($model->comission_sell_book)) {
                $model->comission_sell_book = config('litlife.comission_from_refrence_seller');
            }
        });
        parent::boot();
    }

    public function referred_by_user()
    {
        return $this->belongsTo('App\User', 'referred_by_user_id');
    }

    public function referred_user()
    {
        return $this->belongsTo('App\User', 'referred_user_id');
    }
}
