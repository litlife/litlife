<?php

namespace Tests\Feature\Book\File;

use App\BookFileDownloadLog;
use Tests\TestCase;

class BookFilePurgeDownloadLogsTest extends TestCase
{
    public function testPurgeDownloadLogs()
    {
        $log = BookFileDownloadLog::factory()->create();

        $book_file = $log->book_file;

        $this->assertNotNull($book_file);
        $this->assertEquals(1, $book_file->download_logs()->count());

        $book_file->purgeDownloadLogs();

        $this->assertEquals(0, $book_file->download_logs()->count());
    }
}
