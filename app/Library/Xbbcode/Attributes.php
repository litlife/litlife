<?php

namespace App\Library\Xbbcode;

/**
 * Class Attributes
 */
class Attributes extends \Xbbcode\Attributes
{
	/**
	 * @return string
	 */
	public function count()
	{
		return count($this->attributes);
	}
}
