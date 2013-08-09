<?php

namespace NS\CatalogBundle\Service;

use Doctrine\ORM\EntityManager;
use NS\CatalogBundle\Entity\Item;
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
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
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
			->getQuery()
			->setMaxResults($limit)
			->execute();
		if (count($items)) {
			return $items;
		}

		foreach ($fields as $field) {
			$items = $this->createItemQueryBuilder()
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