<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

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
	 * @return Query
	 */
	public function getFindByCategoryQuery(Category $category = null)
	{
		$queryBuilder = $this
			->createQueryBuilder('i')
			->orderBy('i.id', 'DESC');

		if ($category) {
			$queryBuilder
				->where('i.Category = :category')
				->setParameter('category', $category);
		}

		return $queryBuilder->getQuery();
	}
}