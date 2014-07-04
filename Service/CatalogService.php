<?php

namespace NS\CatalogBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\Pagination\PaginationInterface;
use NS\CatalogBundle\Entity\Catalog;
use NS\CatalogBundle\Entity\CatalogRepository;
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
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var CatalogRepository
     */
    private $catalogRepository;

    /**
     * @param ObjectManager      $entityManager
     * @param ItemRepository     $itemRepository
     * @param CategoryRepository $categoryRepository
     * @param CatalogRepository  $catalogRepository
     */
    public function __construct(ObjectManager $entityManager, ItemRepository $itemRepository,
                                CategoryRepository $categoryRepository, CatalogRepository $catalogRepository)
    {
        $this->entityManager      = $entityManager;
        $this->itemRepository     = $itemRepository;
        $this->categoryRepository = $categoryRepository;
        $this->catalogRepository  = $catalogRepository;
    }

    /**
     * Retrieves item by id
     *
     * @param int $itemId
     * @return Item|null
     */
    public function getItem($itemId)
    {
        return $this->itemRepository->find($itemId);
    }

    /**
     * Retrieves item by slug
     *
     * @param string $itemSlug
     * @return Item|null
     */
    public function getItemBySlug($itemSlug)
    {
        return $this->itemRepository->findOneBySlug($itemSlug);
    }

    /**
     * Retrieves items with pagination
     *
     * @param int      $page               page number
     * @param int      $limit              items per page
     * @param null     $visible            visible flag (item.visible)
     * @param Category $category           item category (item.category)
     * @param array    $settings           settings conditions (e.g. ['hit' => '1', 'price' => 10.2])
     * @param array    $orderBy            order conditions (e.g. [['price', 'ASC', 'number'], ['createdAt', 'DESC']]
     * @param string   $search             any value to search
     * @param bool     $subcategoriesItems load subcategories items if category is set
     * @param bool     $isSortable         order by item 'position' field
     * @return PaginationInterface|Item[]
     */
    public function getItemsPaged($page = 1, $limit = 20, $visible = null, Category $category = null,
                                  array $settings = array(), array $orderBy = array(), $search = null,
                                  $subcategoriesItems = false, $isSortable = false)
    {
        return $this->itemRepository->findItemsPaged($page, $limit, $visible, $category, $settings, $orderBy, $search, $subcategoriesItems, $isSortable);
    }

    /**
     * Retrieves items by ids
     *
     * @param int[] $ids
     * @return Item[]
     */
    public function getItemsByIds(array $ids)
    {
        return $this->itemRepository->findByIds($ids);
    }

    /**
     * Updates item object
     *
     * @param Item $item
     */
    public function updateItem(Item $item)
    {
        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    /**
     * Removes item object
     *
     * @param Item $item
     */
    public function removeItem(Item $item)
    {
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

    /**
     * Sets items category
     *
     * @param Item[]   $items
     * @param Category $category
     */
    public function setItemsCategory(array $items, Category $category)
    {
        foreach ($items as $item) {
            $item->setCategory($category);
        }
        $this->entityManager->flush();
    }

    /**
     * Clones items with its settings
     *
     * @param Item[] $items
     */
    public function cloneItems(array $items)
    {
        foreach ($items as $item) {
            // cloning item
            $clonedItem = clone $item;
            $clonedItem->setTitle($item->getTitle() . ' (копия)');

            // persisting cloned item
            $this->entityManager->detach($clonedItem);
            $this->entityManager->persist($clonedItem);

            // cloning settings
            foreach ($item->getRawSettings() as $setting) {
                // cloning setting
                $clonedSetting = clone $setting;
                $clonedSetting->setItem($clonedItem);
                // persisting cloned setting
                $this->entityManager->detach($clonedSetting);
                $this->entityManager->persist($clonedSetting);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * Retrieves category by id
     *
     * @param int $categoryId
     * @return Category|null
     */
    public function getCategory($categoryId)
    {
        return $this->categoryRepository->find($categoryId);
    }

    /**
     * Retrieves category by slug
     *
     * @param string $slug
     * @return Category|null
     */
    public function getCategoryBySlug($slug)
    {
        return $this->categoryRepository->findOneBySlug($slug);
    }

    /**
     * Retrieves catalog by name
     *
     * @param string $name
     * @return Catalog|null
     */
    public function getCatalogByName($name)
    {
        return $this->catalogRepository->findOneByName($name);
    }

    /**
     * Retrieves catalogs
     *
     * @return Catalog[]
     */
    public function getCatalogs()
    {
        return $this->catalogRepository->findAll();
    }

    /**
     * Retrieves catalog by id
     *
     * @param int $id
     * @return Catalog|null
     */
    public function getCatalog($id)
    {
        if (!$id) {
            return null;
        }
        return $this->catalogRepository->find($id);
    }

    /**
     * Retrieves catalog by id or creates new one
     *
     * @param int $id
     * @return Catalog|null
     */
    public function getCatalogOrCreate($id)
    {
        $catalog = $this->getCatalog($id);
        if (!$catalog) {
            $catalog = $this->createCatalog();
        }
        return $catalog;
    }

    /**
     * Creates new catalog instance
     *
     * @return Catalog
     */
    public function createCatalog()
    {
        return new Catalog();
    }

    /**
     * Updates catalog
     *
     * @param Catalog $catalog
     */
    public function updateCatalog(Catalog $catalog)
    {
        $this->entityManager->persist($catalog);
        $this->entityManager->flush();
    }

    /**
     * Removes catalog
     *
     * @param Catalog $catalog
     */
    public function removeCatalog(Catalog $catalog)
    {
        $this->entityManager->remove($catalog);
        $this->entityManager->flush();
    }
}
