<?php

namespace App\Http\Controllers;

use App\Conversation;
use App\Http\Requests\StoreMessage;
use App\Jobs\Mail\NewPersonalMessageJob;
use App\Jobs\Mail\NewPersonalMessageNotificationJob;
use App\Message;
use App\User;
use Illuminate\Http\Response;
use Illuminate\View\View;

class MessageController extends Controller
{
	/**
	 * Вывод всех сообщений
	 *
	 * @param User $user
	 * @return View
	 */
	public function inbox(User $user)
	{
		$this->authorize('view_inbox', $user);

		$participations = $user->participations()
			->with(['latest_message.create_user'])
			->hasMessages()
			->with(['conversation.participations' => function ($query) use ($user) {
				$query->where('user_id', '!=', $user->id);
			}])
			->orderBy('latest_message_id', 'desc')
			->simplePaginate();

		$participations->loadMissing(['conversation.participations.user']);

		$participations->each(function ($item) use ($user) {
			$item->interlocutor = $item->conversation
				->participations
				->where('user_id', '!=', $user->id)
				->first()
				->user;
		});

		$array = [
			'user' => $user,
			'participations' => $participations ?? null
		];

		return view("message.inbox", $array);
	}

	public function index(User $user)
	{
		//$this->authorize('write_private_messages', $user);

		if (auth()->user()->is($user))
			return redirect()->route('users.inbox', auth()->id());

		if (!empty($conversation = Conversation::whereUsers($user->id, auth()->id())->first())) {
			$messages = $conversation->messages()
				->notDeletedForUser(auth()->id())
				->with(['create_user.avatar', 'create_user.avatar'])
				->latest()
				->simplePaginate();

			$participation = $conversation->participations
				->where('user_id', auth()->id())
				->first();

			if (isset($participation)) {
				if ($participation->hasNewMessages()) {

					$participationCloned = clone $participation;
					$participationCloned->noNewMessages();
					$participationCloned->save();

					auth()->user()->flushCacheNewMessages();
				}
			}
		}

		js_put('auth_user_id', auth()->user()->id);

		$array = [
			'user' => $user,
			'messages' => $messages ?? null,
			'conversation' => $conversation ?? null,
			'participation' => $participation ?? null
		];

		if (request()->ajax())
			return view("message.index_ajax", $array);
		else
			return view("message.index", $array);
	}

	/**
	 * Сохранение сообщения
	 *
	 * @param StoreMessage $request
	 * @param User $user
	 * @return mixed
	 * @throws
	 */
	public function store(StoreMessage $request, User $user)
	{
		$this->authorize('write_private_messages', $user);

		$latest_new_participations_count = auth()->user()
			->latest_new_particaipations_for_hour_count();

		if ($latest_new_participations_count > 10)
			return redirect()->back();

		$latest_new_messages_count = auth()->user()
			->messages()
			->where('created_at', '>', now()->subHour())
			->count();

		if ($latest_new_messages_count > 100)
			return redirect()->back();

		$message = new Message($request->all());
		$message->create_user_id = auth()->id();
		$message->recepient_id = $user->id;
		$message->save();

		if (request()->ajax())
			return $message;
		else
			return redirect()->route('users.messages.index', compact('user'));
	}

	/**
	 * Show one message
	 * @param $id
	 * @return View
	 * @throws
	 */
	public function show($id)
	{
		$message = Message::where('id', $id)
			->firstOrFail();

		return view('message.list.default', ['item' => $message]);
	}

	/**
	 * Форма редактирования
	 *
	 * @param Message $message
	 * @return View
	 * @throws
	 */
	public function edit(Message $message)
	{
		$this->authorize('update', $message);

		$array = ['message' => $message];

		if (request()->ajax())
			return view("message.edit_ajax", $array);
		else
			return view("message.edit", $array);
	}

	/**
	 * Сохранение редактированного сообщения
	 *
	 * @param StoreMessage $request
	 * @param Message $message
	 * @return Response
	 * @throws
	 */
	public function update(StoreMessage $request, Message $message)
	{
		$this->authorize('update', $message);

		$message->fill($request->all());
		$message->user_updated_at = now();
		$message->save();

		if (request()->ajax())
			return $message;
		else
			return redirect()->route('users.messages.index', $message->recepient);
	}

	/**
	 * Удаление воостановление сообщения
	 *
	 * @param int $id
	 * @return Message $message
	 * @throws
	 */
	public function destroy($id)
	{
		$message = Message::withTrashed()
			->joinUserDeletions(auth()->user())
			->findOrFail($id);

		if ($message->trashed() or !is_null($message->message_deletions_deleted_at)) {
			$this->authorize('restore', $message);
			$message->restoreForUser(auth()->user());
		} else {
			$this->authorize('delete', $message);
			$message->deleteForUser(auth()->user());
		}

		$message = Message::withTrashed()
			->joinUserDeletions(auth()->user())
			->findOrFail($message->id);

		$message['deleted_at'] = $message['message_deletions_deleted_at'] ?? $message['deleted_at'];

		return $message;
	}
}
