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
	 * @return Query
	 */
	public function getFindByCategoryQuery(Category $category = null)
	{
		$queryBuilder = $this->getQueryBuilder();

		if ($category) {
			$queryBuilder
				->andWhere('i.category = ?1')
				->setParameter(1, $category->getId());
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

		return $queryBuilder->getQuery()->getResult();
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
}