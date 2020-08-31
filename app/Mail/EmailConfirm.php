<?php

namespace App\Mail;

use App\UserEmailToken;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailConfirm extends Mailable
{
	use Queueable, SerializesModels;

	public $token;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct(UserEmailToken $token)
	{
		$this->token = $token;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->from(config('mail.from.address'), __('app.title'))
			->subject(__('email.confirm.title'))
			->view('emails.confirm');
	}
}
