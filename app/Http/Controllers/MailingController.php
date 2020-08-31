<?php

namespace App\Http\Controllers;

use App\Mailing;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MailingController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$this->authorize('manage_mailings', User::class);

		$mailings = Mailing::when($request->sort == 'rating', function ($query) {
			$query->orderBy('priority', 'desc');
		})
			->when($request->sort == 'latest_sent', function ($query) {
				$query->orderBy('sent_at', 'desc');
			})
			->when($request->show == 'sent', function ($query) {
				$query->sent();
			})
			->when($request->show == 'waited', function ($query) {
				$query->waited();
			})
			->simplePaginate(100);

		return view('mailing.index', ['mailings' => $mailings]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$this->authorize('manage_mailings', User::class);

		return view('mailing.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		$this->authorize('manage_mailings', User::class);

		$text = $request->text;

		$array = preg_split("/[\n\r]/", $text);

		foreach ($array as $line) {
			$line = trim($line);

			if (preg_match('/^([[:graph:]]+)([[:space:]]*)([0-9]*)([[:space:]]*)([[:print:]]*)$/iu', $line, $matches)) {
				list(, $email, , $priority, , $name) = $matches;

				if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
					if (!Mailing::whereEmail($email)->first()) {
						$mailing = new Mailing();
						$mailing->email = $email;
						$mailing->priority = $priority;
						$mailing->name = $name;
						$mailing->save();
					}
				}
			}
		}

		return redirect()
			->route('mailings.index')
			->with(['success' => 'Список обновлен']);
	}
}
