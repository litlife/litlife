<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
			'title' => $this->title,
			'writers' => AuthorResource::collection($this->writers),
			'translators' => AuthorResource::collection($this->translators),
			'editors' => AuthorResource::collection($this->editors),
			'compilers' => AuthorResource::collection($this->compilers),
			'illustrators' => AuthorResource::collection($this->illustrators),
			'is_si' => $this->is_si,
			'is_lp' => $this->is_lp,
			'is_collection' => $this->is_collection,
			'ti_lb' => $this->ti_lb,
			'ti_olb' => $this->ti_olb,
			'year_writing' => $this->year_writing,
			'is_public' => $this->is_public,
			'year_public' => $this->year_public,
			'pi_bn' => $this->pi_bn,
			'pi_pub' => $this->pi_pub,
			'pi_city' => $this->pi_city,
			'pi_year' => $this->pi_year,
			'pi_isbn' => $this->pi_isbn,
			'ready_status' => $this->ready_status,
			'age' => $this->age,
			'sequences' => SequenceResource::collection($this->sequences),
		];
	}
}