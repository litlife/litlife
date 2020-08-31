<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserGroupEnum extends Enum
{
	const Administrator = 'administrator';
	const User = 'user';
	const Banned = 'banned';
	const Author = 'author';
	const ActiveCommentator = 'active_commentator';
	const CommentMaster = 'comment_master';
}
