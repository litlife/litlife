<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class NotConnectedScope implements Scope
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
		//$builder->whereNull($model->getTable() . ".connected_at");

		$builder->where(function ($builder) {
			$builder->whereNull('group_id')
				->orWhere('main_in_group', true);
		});
	}
}
