<?php

namespace Tests\Browser;

use Tests\DuskTestCase;

class NotificationTest extends DuskTestCase
{
	public function testPreviewNotification()
	{
		$this->browse(function ($browser) {
			$browser->visit(route('preview.notification'))
				->assertSee(__('notification.greeting'))
				->assertSee(__('notification.test.line'))
				->assertSee(__('notification.test.action'))
				->assertSee(__('notification.sincerely_yours'))
				->assertSee(__('app.name'))
				->assertVisible('a[href="' . route('home') . '"]')
				->assertDontSee('</td>')
				->assertDontSee('</tr>')
				->assertDontSee('<div>')
				->assertDontSee('</div>')
				->assertDontSee('<td>')
				->assertDontSee('<tr>')
				->assertDontSee('<table>')
				->assertDontSee('</table>');
		});
	}
}
