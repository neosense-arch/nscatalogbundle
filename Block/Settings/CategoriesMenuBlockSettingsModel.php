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
	 * @var bool
	 */
	private $isSubmenu = false;

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
	/**
	 * @param boolean $isSubmenu
	 */
	public function setIsSubmenu($isSubmenu)
	{
		$this->isSubmenu = $isSubmenu;
	}
	/**
	 * @return boolean
	 */
	public function getIsSubmenu()
	{
		return $this->isSubmenu;
	}
}
