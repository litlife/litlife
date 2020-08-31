<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TopicLabelEnum extends Enum
{
	const IdeaImplemented = 1;
	const IdeaOnReview = 2;
	const IdeaRejected = 3;
	const IdeaInProgress = 4;
}
