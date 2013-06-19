<?php

namespace NS\CatalogBundle\Block\Settings;

class CategoriesMenuBlockSettingsModel
{
	/**
	 * @var string
	 */
	private $sortOrder;

	/**
	 * @param string $sortOrder
	 */
	public function setSortOrder($sortOrder)
	{
		$this->sortOrder = $sortOrder;
	}

	/**
	 * @return string
	 */
	public function getSortOrder()
	{
		return $this->sortOrder;
	}
}
