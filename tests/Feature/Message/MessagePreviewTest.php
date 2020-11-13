<?php

namespace Tests\Feature\Message;

use App\Message;
use App\User;
use Tests\TestCase;

class MessagePreviewTest extends TestCase
{
    public function testPreview()
    {
        $message = Message::factory()->create(
            [
                'bb_text' => '[quote]quote[/quote]text[quote]quote[/quote]text',
                'recepient_id' => User::factory()->create()->id
            ]
        );

        $this->assertEquals('text text', $message->getPreviewText());

        $message = Message::factory()->create(
            [
                'bb_text' => 'text text',
                'recepient_id' => User::factory()->create()->id
            ]
        );

        $this->assertEquals('text text', $message->getPreviewText());

        $message = Message::factory()->create(
            [
                'bb_text' => 'text [img]http://test/image.jpeg[/img]',
                'recepient_id' => User::factory()->create()->id
            ]
        );

        $this->assertEquals('text '.'('.__('message.image').')', $message->getPreviewText());

        $message = Message::factory()->create(
            [
                'bb_text' => '[quote][color=#343a40][font=Arial]quote[/font][/color]'.
                    '[url=https://litlife.club/][color=#212529][font=Arial]https://litlife.club/[/font][/color][/url]'.
                    '[url=https://litlife.club][color=#212529][font=Arial]https://litlife.club/[/font][/color][/url][/quote]text'.
                    '[quote][color=#343a40][font=Arial]quote[/font][/color][/quote]'.
                    'text',
                'recepient_id' => User::factory()->create()->id
            ]
        );

        $this->assertEquals('text text', $message->getPreviewText());

        $s = '[quote][color=#343a40][font=Arial]цитата[/font][/color]
[url=https://litlife.club/][color=#212529][font=Arial]https://litlife.club/[/font][/color][/url]
[url=https://litlife.club/][color=#212529][font=Arial]https://litlife.club/[/font][/color][/url][/quote]
текст
[quote][color=#343a40][font=Arial]цитата[/font][/color][/quote]
текст[quote][color=#343a40]цитата[/color]
[color=#212529]цитата[/color][/quote]
текст';

        $message = Message::factory()->create(
            [
                'bb_text' => $s,
                'recepient_id' => User::factory()->create()->id
            ]
        );

        $this->assertEquals('текст текст текст', $message->getPreviewText());


        $s = 'test   test';

        $message = Message::factory()->create(
            [
                'bb_text' => $s,
                'recepient_id' => User::factory()->create()->id
            ]
        );

        $this->assertEquals('test   test', $message->getPreviewText());

        $message = Message::factory()->create(
            [
                'bb_text' => '[img]http://test/image.jpeg[/img]',
                'recepient_id' => User::factory()->create()->id
            ]
        );

        $this->assertEquals('('.__('message.image').')', $message->getPreviewText());

        $message = Message::factory()->create(
            [
                'bb_text' => 'текст [youtube]test[/youtube] текст',
                'recepient_id' => User::factory()->create()->id
            ]
        );

        $this->assertEquals('текст ('.__('message.video').') текст', $message->getPreviewText());
    }
}
