<?php

namespace NS\CatalogBundle\QueryBuilder;

use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use NS\CatalogBundle\Entity\Catalog;
use NS\CatalogBundle\Entity\Category;

/**
 * Class ItemQueryBuilder
 *
 * @package NS\CatalogBundle\QueryBuilder
 */
class ItemQueryBuilder extends QueryBuilder
{
	const ORDER_TYPE_STRING  = 'string';
	const ORDER_TYPE_NUMERIC = 'numeric';

	/**
	 * @var string[]
	 */
	private $joined = array();

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
			->joinCategory()
			->andWhere('i.category = :categoryId')
			->setParameter('categoryId', $category->getId());
	}

	/**
	 * @param Catalog $catalog
	 * @return QueryBuilder
	 */
	public function andWhereCatalog(Catalog $catalog)
	{
		return $this
			->joinCategory()
			->joinCatalog()
			->andWhere('catalog.name = :catalogName')
			->setParameter('catalogName', $catalog->getName());
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return $this
	 */
	public function andWhereSetting($name, $value)
	{
		return $this
			->joinRawSettings()
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
			->joinRawSettings()
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
	public function orderBySetting($name, $direction = 'asc', $type = self::ORDER_TYPE_STRING)
	{
		$order = 's.value';
		if ($type === self::ORDER_TYPE_NUMERIC) {
			$order .= '+0';
		}

		return $this
			->joinRawSettings()
			->andWhere('s.name = :name')
			->setParameter('name', $name)
			->orderBy($order, $direction);
	}

	/**
	 * @return ItemQueryBuilder
	 */
	private function joinCategory()
	{
		if (empty($this->joined['category'])) {
			$this->joined['category'] = true;
			$this
				->join('i.category', 'category');
		}
		return $this;
	}

	/**
	 * @return ItemQueryBuilder
	 */
	private function joinCatalog()
	{
		if (empty($this->joined['catalog'])) {
			$this->joined['catalog'] = true;
			$this
				->joinCategory()
				->join('category.catalog', 'catalog');
		}
		return $this;
	}

	/**
	 * @return ItemQueryBuilder
	 */
	private function joinRawSettings()
	{
		if (empty($this->joined['s'])) {
			$this->joined['s'] = true;
			$this
				->leftJoin('i.rawSettings', 's');
		}
		return $this;
	}
}