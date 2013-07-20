<?php

namespace NS\CatalogBundle\QueryBuilder;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use NS\CatalogBundle\Entity\Category;

/**
 * Class ItemQueryBuilder
 *
 * @package NS\CatalogBundle\QueryBuilder
 */
class ItemQueryBuilder extends QueryBuilder
{
	/**
	 * Constructor
	 *
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		parent::__construct($em);

		$this
			->select('i')
			->from('NSCatalogBundle:Item', 'i')
			->orderBy('i.title', 'ASC');
		;
	}

	/**
	 * @param Category $category
	 * @return $this
	 */
	public function andWhereCategory(Category $category)
	{
		$this
			->join('i.category', 'category')
			->andWhere('i.category = :categoryId')
			->setParameter('categoryId', $category->getId());

		return $this;
	}

	/**
	 * @return $this
	 */
	public function andVisible()
	{
		$this->andWhere('i.visible = true');
		return $this;
	}

	/**
	 * @param string $query
	 * @return $this
	 */
	public function search($query)
	{
		$this
			->leftJoin('i.rawSettings', 's')

			->andWhere('i.title LIKE :query1')
			->setParameter('query1', "%{$query}%")

			->orWhere('s.value LIKE :query2')
			->setParameter('query2', "%{$query}%")
		;

		return $this;
	}
}