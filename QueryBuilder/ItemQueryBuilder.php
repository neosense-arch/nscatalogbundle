<?php

namespace NS\CatalogBundle\QueryBuilder;

use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
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
	}

	/**
	 * @param Category $category
	 * @return $this
	 */
	public function andWhereCategory(Category $category)
	{
		return $this
			->join('i.category', 'category')
			->andWhere('i.category = :categoryId')
			->setParameter('categoryId', $category->getId());
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return $this
	 */
	public function andWhereSetting($name, $value)
	{
		return $this
			->leftJoin('i.rawSettings', 's')
			->andWhere('s.name = :name')
			->setParameter('name', $name)
			->andWhere('s.value = :value')
			->setParameter('value', $value);
	}

	/**
	 * @return $this
	 */
	public function andVisible()
	{
		return $this->andWhere('i.visible = true');
	}

	/**
	 * @param string $query
	 * @return $this
	 */
	public function search($query)
	{
		return $this
			->leftJoin('i.rawSettings', 's')

			->andWhere('i.title LIKE :query1')
			->setParameter('query1', "%{$query}%")

			->orWhere('s.value LIKE :query2')
			->setParameter('query2', "%{$query}%")
		;
	}

	public function limit($limit, $skip)
	{
		return $this
			->setMaxResults($limit)
			->setFirstResult($skip);
	}

	/**
	 * @param string $name
	 * @param string $direction
	 * @param string $type
	 * @return QueryBuilder
	 */
	public function orderBySetting($name, $direction = 'asc', $type = 'string')
	{
		return $this
			->leftJoin('i.rawSettings', 's')
			->andWhere('s.name = :name')
			->setParameter('name', $name)
			->orderBy('s.value', $direction);
	}
}