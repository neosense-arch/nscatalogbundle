<?php

namespace NS\CatalogBundle\Block\Settings;

class CategoriesBlockSettingsModel
{
    /**
     * @var int
     */
    private $categoryId;

    /**
     * @var string
     */
    private $sortOrder;

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
