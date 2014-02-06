<?php

namespace NS\CatalogBundle\Service;

use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use NS\CatalogBundle\Entity\Category;
use NS\CatalogBundle\Entity\Item;
use NS\CatalogBundle\Entity\ItemRepository;
use NS\CatalogBundle\QueryBuilder\ItemQueryBuilder;

/**
 * Class ItemService
 *
 * @package NS\CatalogBundle\Service
 */
class ItemService
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @param EntityManager      $entityManager
     * @param PaginatorInterface $paginator
     * @param ItemRepository     $itemRepository
     */
	public function __construct(EntityManager $entityManager, PaginatorInterface $paginator,
        ItemRepository $itemRepository)
	{
        $this->entityManager      = $entityManager;
        $this->paginator          = $paginator;
        $this->itemRepository     = $itemRepository;
	}

	/**
	 * @return ItemQueryBuilder
	 */
	public function createItemQueryBuilder()
	{
		return new ItemQueryBuilder($this->entityManager);
	}

	/**
	 * @param string   $query
	 * @param string[] $fields
	 * @param int      $limit
	 * @return Item[]
	 */
	public function search($query, array $fields = array(), $limit = 30)
	{
		$items = $this->createItemQueryBuilder()
			->andWhereTitleLike($query)
			->andVisible()
			->getQuery()
			->setMaxResults($limit)
			->execute();
		if (count($items)) {
			return $items;
		}

		foreach ($fields as $field) {
			$items = $this->createItemQueryBuilder()
				->andVisible()
				->join('i.rawSettings', 's')
				->andwhere('s.name = :settingName')
				->andWhere('s.value LIKE :settingValue')
				->getQuery()
				->setMaxResults($limit)
				->execute(array(
					'settingName' => $field,
					'settingValue' => "%{$query}%",
				));
			if (count($items)) {
				return $items;
			}
		}

		return array();
	}
}