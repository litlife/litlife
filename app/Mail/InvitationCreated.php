<?php

namespace App\Mail;

use App\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationCreated extends Mailable
{
	use Queueable, SerializesModels;

	public $invitation;

	/**
	 * Create a new message instance.
	 * @param Invitation $invitation
	 * @return void
	 */
	public function __construct(Invitation $invitation)
	{
		$this->invitation = $invitation;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->from(config('mail.from.address'), __('app.title'))
			->subject(__('email.invitation.title'))->view('emails.invitation');
	}
}
