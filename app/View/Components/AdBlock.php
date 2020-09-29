<?php

namespace App\View\Components;

class AdBlock extends Component
{
	private $name;
	private $code = '';
	private $block;

	/**
	 * Create a new component instance.
	 *
	 * @param string $name
	 * @return void
	 */
	public function __construct($name)
	{
		$this->name = $name;
		$this->block = \App\AdBlock::name($name)->first();

		if ($this->block)
			$this->code = $this->block->code;
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\View\View|string
	 */
	public function render()
	{
		return $this->code;
	}
}
