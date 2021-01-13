<?php

namespace App\Http\Controllers;

use App\Http\SearchResource\AuthorSearchResource;
use App\Http\SearchResource\SequenceSearchResource;
use App\Library\SearchResource;
use App\Sequence;
use App\User;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SequenceListController extends Controller
{
	/**
	 * Список серий
	 *
	 * @return View
	 */
	function index(Request $request)
	{
        $builder = Sequence::notMerged()
            ->acceptedOrBelongsToAuthUser();

        $resource = (new SequenceSearchResource($request, $builder))
            ->defaultSorting('name_asc');

        return $resource->view();
	}

	/**
	 * Серии пользователя
	 *
	 * @param User $user
	 * @return View
	 */
	public function userCreated(Request $request, User $user)
	{
        $builder = $user->created_sequences()->withoutCheckedScope();

        $resource = (new SequenceSearchResource($request, $builder))
            ->setSimplePaginate(false)
            ->defaultSorting('latest');

        return $resource->view();
	}

	/**
	 * Серии в избранном пользователя
	 *
     * @param Request $request
	 * @param User $user
	 * @return View
	 */
	function userLibrary(Request $request, User $user)
	{
        $builder = $user->sequences()
            ->withPivot('created_at')
            ->any();

        $resource = (new SequenceSearchResource($request, $builder))
            ->setSimplePaginate(false)
            ->defaultSorting('user_sequences_created_at_asc')
            ->addOrder('user_sequences_created_at_asc', function ($query) {
                $query->orderBy('pivot_created_at', 'asc')
                    ->orderBy('sequences.id', 'desc');
            })
            ->addOrder('user_sequences_created_at_desc', function ($query) {
                $query->orderBy('pivot_created_at', 'desc')
                    ->orderBy('sequences.id', 'asc');
            });

        return $resource->view();
	}
}
