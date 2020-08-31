<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\StoreSequence;
use App\Jobs\Sequence\UpdateSequenceBooksCount;
use App\Library\CommentSearchResource;
use App\Sequence;
use App\UserSequence;
use Coderello\SharedData\Facades\SharedData;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SequenceController extends Controller
{
	/**
	 * Форма создания
	 *
	 * @return View
	 * @throws
	 */
	public function create()
	{
		$this->authorize('create', Sequence::class);

		return view('sequence.create');
	}

	/**
	 * Сохранение
	 *
	 * @param StoreSequence $request
	 * @return Response
	 * @throws
	 */
	public function store(StoreSequence $request)
	{
		$this->authorize('create', Sequence::class);

		$sequence = new Sequence;
		$sequence->fill($request->all());
		$sequence->user_edited_at = now();
		$sequence->save();

		return redirect()
			->route('sequences.show', $sequence);
	}

	/**
	 * Страница серии
	 *
	 * @param Sequence $sequence
	 * @return View
	 */
	public function show(Sequence $sequence)
	{
		SharedData::put(['sequence_id' => $sequence->id]);

		$sequence->load(['likes' => function ($query) {
			$query->where('create_user_id', auth()->id());
		}]);

		$sequence->load(['library_users' => function ($query) {
			$query->where('user_id', auth()->id());
		}]);

		if (!$sequence->isHaveAccess())
			return view('sequence.show_access_denied', compact(
				'sequence'
			));

		return response()->view('sequence.show',
			['sequence' => $sequence, 'books' => $sequence->books()->acceptedOrBelongsToAuthUser()->forTable()->get()],
			$sequence->trashed() ? 404 : 200);
	}

	/**
	 * Форма редактирования
	 *
	 * @param Sequence $sequence
	 * @return View
	 * @throws
	 */
	public function edit(Sequence $sequence)
	{
		$this->authorize('update', $sequence);

		return view('sequence.edit', compact('sequence'));
	}

	/**
	 * Сохранение отредактированного
	 *
	 * @param StoreSequence $request
	 * @param Sequence $sequence
	 * @return Response
	 * @throws
	 */
	public function update(StoreSequence $request, Sequence $sequence)
	{
		$this->authorize('update', $sequence);

		$sequence->fill($request->all());
		$sequence->user_edited_at = now();
		$sequence->save();

		return back();
	}

	/**
	 * Удаление и восстановление
	 *
	 * @param Sequence $sequence
	 * @return Response
	 * @throws
	 */
	public function delete(Sequence $sequence)
	{
		if ($sequence->trashed()) {
			$this->authorize('restore', $sequence);

			$sequence->restore();

			activity()->performedOn($sequence)
				->log('restored');
		} else {
			$this->authorize('delete', $sequence);

			$sequence->delete();

			activity()->performedOn($sequence)
				->log('deleted');
		}

		return redirect()
			->route('sequences.show', $sequence);
	}

	/**
	 * Добавление или удаление автора в личную библиотеку
	 *
	 * @param Sequence $sequence
	 * @return array
	 * @throws
	 */
	public function toggle_my_library(Sequence $sequence)
	{
		$user_sequence_pivot = UserSequence::where('sequence_id', $sequence->id)
			->where('user_id', auth()->id())
			->first();

		if (empty($user_sequence_pivot)) {
			UserSequence::create(['sequence_id' => $sequence->id]);
			$sequence->refresh();

			return [
				'result' => 'attached',
				'added_to_favorites_count' => $sequence->added_to_favorites_count
			];
		} else {

			$user_sequence_pivot->delete();
			$sequence->refresh();

			return [
				'result' => 'detached',
				'added_to_favorites_count' => $sequence->added_to_favorites_count
			];
		}
	}

	/**
	 * Поиск серии js
	 *
	 * @param Request $request
	 * @return Response
	 * @throws
	 */
	public function search(Request $request)
	{
		$q = $request->input('q');

		$query = Sequence::void()
			->notMerged()
			->acceptedOrBelongsToAuthUser()
			->fulltextSearch($q)
			->orWhere('id', pg_intval($q));

		return $query->simplePaginate();
	}

	/**
	 * Форма для заполнения номеров книг в серии
	 *
	 * @param Sequence $sequence
	 * @return View
	 * @throws
	 */
	public function book_numbers(Sequence $sequence)
	{
		$this->authorize('book_numbers_edit', $sequence);

		$books = $sequence->books()
			->withPivot('number')
			->orderBy('number')
			->notConnected()
			->get();

		return view('sequence.book_numbers', compact('sequence', 'books'));
	}

	/**
	 * Сохранение книг в серии
	 *
	 * @param Request $request
	 * @param Sequence $sequence
	 * @return Response
	 * @throws
	 */
	public function book_numbers_save(Request $request, Sequence $sequence)
	{
		$this->authorize('book_numbers_edit', $sequence);

		if (isset($request->numbers) and is_array($request->numbers) and count($request->numbers) > 0) {
			foreach ($request->numbers as $book_id => $number) {
				$sequence->books()->updateExistingPivot($book_id, ['number' => $number]);
			}
		} else {
			return redirect()
				->route('sequences.book_numbers', $sequence);
		}

		return redirect()
			->route('sequences.book_numbers', $sequence)
			->with(['success' => __('sequence.book_numbers_in_the_series_have_been_successfully_changed')]);
	}

	/**
	 * Форма объединения серии с другой
	 *
	 * @param Sequence $sequence
	 * @return View
	 * @throws
	 */
	public function mergeForm(Sequence $sequence)
	{
		$this->authorize('merge', $sequence);

		return view('sequence.merge', compact('sequence'));
	}

	/**
	 * Объединеняет серию и переносит книгу
	 *
	 * @param Request $request
	 * @param Sequence $sequence
	 * @return Response
	 * @throws
	 */
	public function merge(Request $request, Sequence $sequence)
	{
		$this->authorize('merge', $sequence);

		$this->validate($request, ['merged_to_sequence_id' => 'required|numeric'], [], __('sequence'));

		if (empty($merged_to_sequence = Sequence::find($request->merged_to_sequence_id))) {
			return back()->withInput()->withErrors('Серия не найдена');
		}

		$this->authorize('merge', $merged_to_sequence);

		DB::transaction(function () use ($sequence, $merged_to_sequence) {
			$sequence->merged_at = now();
			$sequence->merge_user_id = auth()->id();
			$sequence->merged_to = $merged_to_sequence->id;
			$sequence->save();

			$book_ids = $sequence->books()->any()->get()->pluck('id')->toArray();

			$sequence->books()->detach($book_ids);
			$merged_to_sequence->books()->syncWithoutDetaching($book_ids);

			UpdateSequenceBooksCount::dispatch($sequence);
			UpdateSequenceBooksCount::dispatch($merged_to_sequence);

			activity()
				->performedOn($sequence)
				->log('merged');
		});

		return redirect()->route('sequences.show', $merged_to_sequence);
	}

	/**
	 * Отсоединяет серию
	 *
	 * @param Request $request
	 * @param Sequence $sequence
	 * @return Response
	 * @throws
	 */
	public function unmerge(Request $request, Sequence $sequence)
	{
		$this->authorize('unmerge', $sequence);

		$sequence->merged_at = null;
		$sequence->merge_user_id = null;
		$sequence->merged_to = null;
		$sequence->save();

		return back();
	}

	public function activity_logs(Sequence $sequence)
	{
		$activityLogs = $sequence->activities()
			->latest()
			->simplePaginate();

		$activityLogs->load(['causer', 'subject' => function ($query) {
			$query->any();
		}]);

		return view('activity_log.index', compact('activityLogs'));
	}

	public function books(Sequence $sequence)
	{
		return view('sequence.books', ['books' => $sequence->books()->forTable()->get()]);
	}

	public function comments(Sequence $sequence)
	{
		$books_ids = $sequence->books()
			->select('id')
			->pluck('id')
			->toArray();

		if (count($books_ids) < 1) $books_ids = [];

		$builder = Comment::whereIn('commentable_id', $books_ids)
			->book();

		$resource = (new CommentSearchResource(request(), $builder))
			->setViewType('comment.list.default');

		$vars = $resource->getVars();

		$vars['sequence'] = $sequence;
		$vars['comments'] = $resource->getQuery()->simplePaginate();

		if (request()->ajax()) {
			if (request()->with_panel)
				return view('sequence.comments', $vars)
					->renderSections()['content'];
			else
				return view('comment.list', $vars);
		}

		return view('sequence.comments', $vars);
	}
}
