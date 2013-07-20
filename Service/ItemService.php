<?php

namespace NS\CatalogBundle\Service;

use Doctrine\ORM\EntityManager;
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
}