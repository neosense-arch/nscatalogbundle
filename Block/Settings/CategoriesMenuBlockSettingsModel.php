<?php

namespace NS\CatalogBundle\Block\Settings;

/**
 * Class CategoriesMenuBlockSettingsModel
 *
 * @package NS\CatalogBundle\Block\Settings
 */
class CategoriesMenuBlockSettingsModel
{
	/**
	 * @var string
	 */
	private $sortOrder;

	/**
	 * @var string
	 */
	private $template = 'NSCatalogBundle:Blocks:categoriesMenuBlock.html.twig';

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
	/**
	 * @param string $template
	 */
	public function setTemplate($template)
	{
		$this->template = $template;
	}
	/**
	 * @return string
	 */
	public function getTemplate()
	{
		return $this->template;
	}
}
