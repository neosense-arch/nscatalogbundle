<?php

namespace NS\CatalogBundle\Service;

use Knp\Component\Pager\Pagination\PaginationInterface;
use NS\CatalogBundle\Entity\Category;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Entity\Item;
use NS\CatalogBundle\Entity\ItemRepository;

/**
 * Class CatalogService
 *
 * @package NS\CatalogBundle\Service
 */
class CatalogService
{
    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @param ItemRepository     $itemRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(ItemRepository $itemRepository, CategoryRepository $categoryRepository)
    {
        $this->itemRepository     = $itemRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Retrieves items with pagination
     *
     * @param int      $page     page number
     * @param int      $limit    items per page
     * @param null     $visible  visible flag (item.visible)
     * @param Category $category item category (item.category)
     * @param array    $settings settings conditions (e.g. ['hit' => '1', 'price' => 10.2])
     * @param array    $orderBy  order conditions (e.g. [['price', 'ASC', 'number'], ['createdAt', 'DESC']]
     * @return PaginationInterface|Item[]
     */
    public function getItemsPaged($page = 1, $limit = 20, $visible = null, Category $category = null,
                                  array $settings = array(), array $orderBy = array())
    {
        return $this->itemRepository->findItemsPaged($page, $limit, $visible, $category, $settings, $orderBy);
    }
} 