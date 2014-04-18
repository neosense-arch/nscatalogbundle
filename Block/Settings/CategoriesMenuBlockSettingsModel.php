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
	 * @var bool
	 */
	private $isSubmenu = false;

	/**
	 * @var string
	 */
	private $routeName = 'ns_catalog_category';

	/**
	 * @var bool
	 */
	private $redirectToFirstItem = false;

    /**
     * @var int
     */
    private $categoryId;

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
	/**
	 * @param string $routeName
	 */
	public function setRouteName($routeName)
	{
		$this->routeName = $routeName;
	}
	/**
	 * @return string
	 */
	public function getRouteName()
	{
		return $this->routeName;
	}
	/**
	 * @param boolean $redirectToFirstItem
	 */
	public function setRedirectToFirstItem($redirectToFirstItem)
	{
		$this->redirectToFirstItem = $redirectToFirstItem;
	}
	/**
	 * @return boolean
	 */
	public function getRedirectToFirstItem()
	{
		return $this->redirectToFirstItem;
	}

    /**
     * @param int $categoryId
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

}
