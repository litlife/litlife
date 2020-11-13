<?php

namespace Tests\Unit\Forum;

use App\Topic;
use PHPUnit\Framework\TestCase;

class TopicIsArchivedTest extends TestCase
{
    public function testDefault()
    {
        $topic = new Topic();

        $this->assertFalse($topic->isArchived());
    }

    public function testIsArchivedTrue()
    {
        $topic = new Topic();
        $topic->archived = true;

        $this->assertTrue($topic->isArchived());
    }

    public function testIsArchivedFalse()
    {
        $topic = new Topic();
        $topic->archived = false;

        $this->assertFalse($topic->isArchived());
    }
}
