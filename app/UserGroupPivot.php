<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\UserGroupPivot
 *
 * @property int $user_id
 * @property int $user_group_id
 * @property \Illuminate\Support\Carbon|null $created_at Время создания данных
 * @property \Illuminate\Support\Carbon|null $updated_at Время обновления данных
 * @method static \Illuminate\Database\Eloquent\Builder|UserGroupPivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserGroupPivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserGroupPivot query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserGroupPivot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserGroupPivot whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserGroupPivot whereUserGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserGroupPivot whereUserId($value)
 * @mixin \Eloquent
 */
class UserGroupPivot extends Pivot
{

}
