<?php

namespace Litlife\Fb2;

class FictionBook extends Tag
{
	public function description()
	{
		return $this->getFb2()->description();
	}
}