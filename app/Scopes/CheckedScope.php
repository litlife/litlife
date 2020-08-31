<?php

namespace App\Scopes;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CheckedScope implements Scope
{
	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param Builder $builder
	 * @param Model $model
	 * @return void
	 */
	public function apply(Builder $builder, Model $model)
	{
		$builder->where($model->getTable() . '.' . $model->getStatusColumn(), StatusEnum::Accepted);

		if (auth()->check())
			$builder->orWhere($model->getTable() . '.create_user_id', auth()->id());
	}
}
