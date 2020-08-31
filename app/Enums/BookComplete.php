<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class BookComplete extends Enum
{
	const complete = 1;
	const complete_but_publish_only_part = 2;
	const not_complete_but_still_writing = 3;
	const not_complete_and_not_will_be = 4;
}
