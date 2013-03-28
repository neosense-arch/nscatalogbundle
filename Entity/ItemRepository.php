<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use NS\CatalogBundle\Model\AbstractSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormTypeInterface;

class ItemRepository extends EntityRepository
{
	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @param ContainerInterface $container
	 */
	public function setContainer(ContainerInterface $container)
	{
		$this->container = $container;
	}

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
		$items = $this->findAll();
		return $this->filterItemsBySettings($items, $key, $value, $limit, $skip);
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
		$items = $this->findAllVisible();
		return $this->filterItemsBySettings($items, $key, $value, $limit, $skip);
	}

	/**
	 * @param Item[] $items
	 * @param string $key
	 * @param mixed  $value
	 * @param int    $limit
	 * @param int    $skip
	 * @return Item[]
	 * @throws \Exception
	 */
	private function filterItemsBySettings($items, $key, $value, $limit = null, $skip = 0)
	{
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