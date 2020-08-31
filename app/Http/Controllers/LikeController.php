<?php

namespace App\Http\Controllers;

use App\Like;
use Illuminate\Database\Eloquent\Relations\Relation;

class LikeController extends Controller
{
	/**
	 * Установка и удаление лайка
	 *
	 * @param string $type
	 * @param int $id
	 * @return array
	 * @throws
	 */
	function like($type, $id)
	{
		$this->authorize('create', Like::class);

		$map = Relation::morphMap();

		if (!isset($map[$type]))
			abort(404);
		else
			$model = $map[$type];

		$item = $model::any()->find($id);

		$like = $item->likes()
			->withTrashed()
			->firstOrNew(['create_user_id' => auth()->id()]);

		if (!empty($item->create_user->id) and $item->create_user->id == auth()->id()) {
			if ($like->exists)
				$like->delete();
		} else {
			if ($like->exists) {
				if ($like->trashed())
					$like->restore();
				else
					$like->delete();
			} else {
				$like->save();
			}
		}

		$html = $this->getToolTipHtml($item);

		return [
			'item' => $like->likeable,
			'like' => $like,
			'latest_likes_html' => $html
		];
	}

	protected function getToolTipHtml($item)
	{
		$latest_likes = $item->likes()
			->with('create_user.avatar')
			->latest()
			->limit(5)
			->get();

		return view('like.tooltip', ['likes' => $latest_likes])
			->render();
	}

	function tooltip($type, $id)
	{
		$map = Relation::morphMap();

		if (!isset($map[$type]))
			abort(404);
		else
			$model = $map[$type];

		$item = $model::any()->find($id);

		return $this->getToolTipHtml($item);
	}
}
