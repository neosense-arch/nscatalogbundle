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
		$items = $this->findAll();

		$filtered = array_filter($items, function(Item $item) use($key, $value){
			$settings = $item->getSettings();
			$method = 'get' . ucfirst($key);
			if (!method_exists($settings, $method)) {
				throw new \Exception(sprintf("Method '%s::%s' wasn't found", get_class($settings), $method));
			}
			return $settings->$method() == $value;
		});

		if ($limit) {
			return array_slice($filtered, $skip, $limit);
		}

		return $filtered;
	}
}