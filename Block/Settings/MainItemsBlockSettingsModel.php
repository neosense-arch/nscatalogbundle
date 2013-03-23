<?php

namespace NS\CatalogBundle\Block\Settings;

class MainItemsBlockSettingsModel
{
	/**
	 * @var string
	 */
	private $count = 5;

	/**
	 * @param string $count
	 */
	public function setCount($count)
	{
		$this->count = $count;
	}

	/**
	 * @return string
	 */
	public function getCount()
	{
		return $this->count;
	}
}
