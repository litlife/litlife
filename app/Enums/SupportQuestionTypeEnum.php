<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class SupportQuestionTypeEnum extends Enum
{
	const Finance = 1;
	const TechnicalProblem = 2;
	const Other = 50;
}
