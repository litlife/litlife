<?php

namespace App\Mail;

use App\PasswordReset;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPasswordReset extends Mailable
{
	use Queueable, SerializesModels;

	public $reset;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct(PasswordReset $reset)
	{
		$this->reset = $reset;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->from(config('mail.from.address'), __('app.title'))
			->subject(__('email.password_reset.title'))
			->view('emails.password_reset');
	}
}
