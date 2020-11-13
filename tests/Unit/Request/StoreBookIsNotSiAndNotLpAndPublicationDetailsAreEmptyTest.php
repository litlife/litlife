<?php

namespace Tests\Unit\Request;

use App\Http\Requests\StoreBook;
use PHPUnit\Framework\TestCase;

class StoreBookIsNotSiAndNotLpAndPublicationDetailsAreEmptyTest extends TestCase
{
    public function testIsSiTrue()
    {
        $request = new StoreBook(['is_si' => true]);

        $this->assertFalse($request->isNotSiAndNotLpAndPublicationDetailsAreEmpty());
    }

    public function testIsLpTrue()
    {
        $request = new StoreBook(['is_lp' => true]);

        $this->assertFalse($request->isNotSiAndNotLpAndPublicationDetailsAreEmpty());
    }

    public function testIsPublisherFilled()
    {
        $request = new StoreBook(['pi_pub' => 'test']);

        $this->assertFalse($request->isNotSiAndNotLpAndPublicationDetailsAreEmpty());
    }

    public function testEmpty()
    {
        $request = new StoreBook([
            'is_si' => false,
            'is_lp' => false,
            'pi_pub' => ''
        ]);

        $this->assertTrue($request->isNotSiAndNotLpAndPublicationDetailsAreEmpty());
    }
}
