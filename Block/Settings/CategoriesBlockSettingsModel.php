<?php

namespace NS\CatalogBundle\Block\Settings;

class CategoriesBlockSettingsModel
{
    /**
     * @var int
     */
    private $categoryId;

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
