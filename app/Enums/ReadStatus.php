<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ReadStatus extends Enum
{
	const Null = 'null';
	const Readed = 'readed';
	const ReadLater = 'read_later';
	const ReadNow = 'read_now';
	const ReadNotComplete = 'read_not_complete';
	const NotRead = 'not_read';
}
