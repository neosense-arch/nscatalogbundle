<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use NS\CatalogBundle\QueryBuilder\ItemQueryBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ItemRepository
 *
 * @package NS\CatalogBundle\Entity
 */
class ItemRepository extends EntityRepository
{
	/**
	 * @param  int $id
	 * @return Item|null
	 */
	public function findOneById($id)
	{
		return $this->findOneBy(array('id' => $id));
	}

	/**
	 * @param  string $slug
	 * @return Item|null
	 */
	public function findOneBySlug($slug)
	{
		return $this->findOneBy(array('slug' => $slug));
	}

	/**
	 * @param  int[] $ids
	 * @return Item[]
	 */
	public function findByIds(array $ids)
	{
		$queryBuilder = new ItemQueryBuilder($this->_em);
		$queryBuilder
			->where($queryBuilder->expr()->in('i.id', '?1'))
			->setParameter(1, $ids);

		return $queryBuilder->getQuery()->getResult();
	}

	/**
	 * @return Item[]
	 */
	public function findAll()
	{
		return $this
			->getFindByCategoryQuery()
			->execute();
	}

	/**
	 * @return Item[]
	 */
	public function findAllVisible()
	{
		$queryBuilder = new ItemQueryBuilder($this->_em);
		return $queryBuilder
			->andWhere('i.visible = true')
			->getQuery()
			->getResult();
	}

	/**
	 * @param Category $category
	 * @return Item[]
	 */
	public function findByCategory(Category $category = null)
	{
		return $this
			->getFindByCategoryQuery($category)
			->execute();
	}

	/**
	 * @param  Category $category
	 * @param  string   $search
	 * @return Query
	 */
	public function getFindByCategoryQuery(Category $category = null, $search = null)
	{
		$queryBuilder = new ItemQueryBuilder($this->_em);

		if ($category) {
			$queryBuilder->andWhereCategory($category);
		}

		if ($search) {
			$queryBuilder->search($search);
		}

		return $queryBuilder->getQuery();
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 * @param int    $limit
	 * @param int    $skip
	 * @param int    $skip
	 * @return Item[]
	 */
	public function findVisibleBySettings($key, $value, $limit = null, $skip = 0)
	{
		$queryBuilder = new ItemQueryBuilder($this->_em);
		$queryBuilder
			->leftJoin('i.rawSettings', 's')
			->andWhere('i.visible = true')
			->andWhere('s.name = :name')
			->setParameter('name', $key)
			->andWhere('s.value = :value')
			->setParameter('value', $value);

		if ($limit) {
			$queryBuilder
				->setMaxResults($limit)
				->setFirstResult($skip);
		}

		return $queryBuilder->getQuery()->getResult();
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 * @param int    $limit
	 * @param int    $skip
	 * @return Query
	 */
	public function getFindBySettingsQuery($key, $value, $limit = null, $skip = 0)
	{
		$queryBuilder = $this
			->getFindBySettingsQueryBuilder($key, $value, $limit, $skip);

		return $queryBuilder->getQuery();
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 * @param int    $limit
	 * @param int    $skip
	 * @return Query
	 */
	public function getFindVisibleBySettingsQuery($key, $value, $limit = null, $skip = 0)
	{
		$queryBuilder = $this
			->getFindBySettingsQueryBuilder($key, $value, $limit, $skip)
			->andWhere('i.visible = true');

		return $queryBuilder->getQuery();
	}

	/**
	 * @param int $limit
	 * @param int $skip
	 * @return Query
	 */
	public function getFindFullCatalogQuery($limit = null, $skip = 0)
	{
		$queryBuilder = new ItemQueryBuilder($this->_em);

		$queryBuilder
			->join('i.category', 'c')
			->where('i.visible = true')
			->addOrderBy('c.title')
			->addOrderBy('i.title');

		if ($limit) {
			$queryBuilder
				->setMaxResults($limit)
				->setFirstResult($skip);
		}

		return $queryBuilder->getQuery();
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 * @param int    $limit
	 * @param int    $skip
	 * @return QueryBuilder
	 */
	private function getFindBySettingsQueryBuilder($key, $value, $limit = null, $skip = 0)
	{
		$queryBuilder = new ItemQueryBuilder($this->_em);
		$queryBuilder->andWhereSetting($key, $value);

		if ($limit) {
			$queryBuilder->limit($limit, $skip);
		}

		return $queryBuilder;
	}
}