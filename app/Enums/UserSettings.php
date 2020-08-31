<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserSettings extends Enum
{
	// TODO планирую переделать все настройки пользователя в формат key value

	const LoginWithID = 1;
	const BookmarkFoldersOrder = 2;
	const BlacklistGenres = 3;

	const PermissionsWriteOnTheWall = 4;
	const PermissionsCommentOnTheWall = 5;
	const PermissionsWritePrivateMessages = 6;
}
