<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
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
	 * @return Item
	 */
	public function findOneById($id)
	{
		return $this->findOneBy(array('id' => $id));
	}

	/**
	 * @param  int[] $ids
	 * @return Item[]
	 */
	public function findByIds(array $ids)
	{
		$queryBuilder = $this->createQueryBuilder('i');

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
		return $this->getQueryBuilder()
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
	 * @return QueryBuilder
	 */
	private function getQueryBuilder()
	{
		return $this
			->createQueryBuilder('i')
			->leftJoin('i.rawSettings', 's')
			->orderBy('i.title', 'ASC');
	}

	/**
	 * @param  Category $category
	 * @param  string   $search
	 * @return Query
	 */
	public function getFindByCategoryQuery(Category $category = null, $search = null)
	{
		$queryBuilder = $this->getQueryBuilder();

		if ($category) {
			$queryBuilder
				->andWhere('i.category = ?1')
				->setParameter(1, $category->getId());
		}

		if ($search) {
			$queryBuilder
				->andWhere('i.title LIKE :query1')
				->setParameter('query1', '%' . $search . '%')
				->orWhere('s.value LIKE :query2')
				->setParameter('query2', '%' . $search . '%');
		}

		return $queryBuilder->getQuery();
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 * @param int    $limit
	 * @param int    $skip
	 * @return Item[]
	 * @throws \Exception
	 */
	public function findBySettings($key, $value, $limit = null, $skip = 0)
	{
		return $this
			->getFindBySettingsQuery($key, $value, $limit, $skip)
			->getResult();
	}

	/**
	 * @param string $key
	 * @param mixed  $value
	 * @param int    $limit
	 * @param int    $skip
	 * @return Item[]
	 */
	public function findVisibleBySettings($key, $value, $limit = null, $skip = 0)
	{
		$queryBuilder = $this->getQueryBuilder()
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
	 * @param string $key
	 * @param mixed  $value
	 * @param int    $limit
	 * @param int    $skip
	 * @return QueryBuilder
	 */
	private function getFindBySettingsQueryBuilder($key, $value, $limit = null, $skip = 0)
	{
		$queryBuilder = $this->getQueryBuilder()
			->andWhere('s.name = :name')
			->setParameter('name', $key)
			->andWhere('s.value = :value')
			->setParameter('value', $value);

		if ($limit) {
			$queryBuilder
				->setMaxResults($limit)
				->setFirstResult($skip);
		}

		return $queryBuilder;
	}
}