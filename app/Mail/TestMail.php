<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
	use Queueable, SerializesModels;

	public $test_mail_text;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct($request)
	{
		$this->test_mail_text = $request->test_mail_text;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->from(config('mail.from.address'), __('app.title'))
			->subject('Тестовое письмо')
			->view('emails.test');
	}
}
