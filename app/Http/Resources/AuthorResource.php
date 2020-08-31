<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param Request $request
	 * @return array
	 */
	public function toArray($request)
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'middle_name' => $this->middle_name,
			'nickname' => $this->nickname,
			'books_count' => $this->books_count,
			'created_at' => $this->created_at,
			'deleted_at' => $this->deleted_at,
		];
	}
}