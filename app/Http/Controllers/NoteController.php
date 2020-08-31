<?php

namespace App\Http\Controllers;

use App\Book;
use App\Events\Book\NotesCountChanged;
use App\Events\Book\SectionsCountChanged;
use App\Jobs\Book\UpdateBookNotesCount;
use App\Jobs\Book\UpdateBookSectionsCount;
use App\Section;
use Illuminate\Http\Request;

class NoteController extends SectionController
{
	/**
	 * Перенос сноски в главы
	 *
	 * @param Book $book
	 * @param Request $request
	 * @return array
	 * @throws
	 */
	public function moveToSections(Book $book, Request $request)
	{
		$this->authorize('move_sections_to_notes', $book);

		$notes_ids = (array)$request->notes_ids;

		if (!empty($notes_ids)) {

			Section::where('book_id', $book->id)
				->whereIn('id', $notes_ids)
				->update(['type' => 'section']);

			Section::scoped(['book_id' => $book->id, 'type' => 'section'])->fixTree();
			Section::scoped(['book_id' => $book->id, 'type' => 'note'])->fixTree();
		}

		UpdateBookSectionsCount::dispatch($book);
		UpdateBookNotesCount::dispatch($book);

		$book->user_edited_at = now();
		$book->edit_user_id = auth()->id();
		$book->changed();
		$book->save();

		$book->updatePageNumbers();

		return ['notes_ids' => $notes_ids];
	}
}