<?php

namespace Tests\Feature\Notification;

use App\Notifications\NewSupportResponseNotification;
use App\SupportQuestionMessage;
use Tests\TestCase;

class NewSupportResponseNotificationTest extends TestCase
{
	public function testVia()
	{
		$message = factory(SupportQuestionMessage::class)
			->create();

		$notification = new NewSupportResponseNotification($message);

		$this->assertEquals(['mail', 'database'], $notification->via($message));
	}

	public function testMail()
	{
		$message = factory(SupportQuestionMessage::class)
			->create();

		$notification = new NewSupportResponseNotification($message);

		$mail = $notification->toMail($message);

		$this->assertEquals(__('New response from the support service'), $mail->subject);

		$this->assertEquals(__('We have answered your question with the subject «:title»', ['title' => $message->supportQuestion->title]),
			$mail->introLines[0]);

		$this->assertEquals(route('support_questions.show', ['support_question' => $message->supportQuestion->id]),
			$mail->actionUrl);

		$this->assertEquals(__('Go to answer'), $mail->actionText);
	}

	public function testDatabase()
	{
		$message = factory(SupportQuestionMessage::class)
			->create();

		$notification = new NewSupportResponseNotification($message);

		$database = $notification->toArray($message);

		$this->assertEquals(__('New response from the support service'), $database['title']);

		$this->assertEquals(__('We have answered your question with the subject «:title»', ['title' => $message->supportQuestion->title]),
			$database['description']);

		$this->assertEquals(route('support_questions.show', ['support_question' => $message->supportQuestion->id]),
			$database['url']);
	}
}
