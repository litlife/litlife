<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * App\UserGroupPivot
 *
 * @property int $user_id
 * @property int $user_group_id
 * @property Carbon|null $created_at Время создания данных
 * @property Carbon|null $updated_at Время обновления данных
 * @method static Builder|UserGroupPivot newModelQuery()
 * @method static Builder|UserGroupPivot newQuery()
 * @method static Builder|UserGroupPivot query()
 * @method static Builder|UserGroupPivot whereCreatedAt($value)
 * @method static Builder|UserGroupPivot whereUpdatedAt($value)
 * @method static Builder|UserGroupPivot whereUserGroupId($value)
 * @method static Builder|UserGroupPivot whereUserId($value)
 * @mixin Eloquent
 */
class UserGroupPivot extends Pivot
{

}
