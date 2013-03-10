<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class CategoryRepository extends EntityRepository
{
	/**
	 * @return array
	 */
	public function findForDynatree()
	{
		$categories = $this->findBy(array(), array('root' => 'ASC', 'left' => 'ASC', 'title' => 'ASC'));
		return $this->mapCategories($categories);
	}

	/**
	 * @return Category
	 */
	public function findRootOrCreate()
	{
		$root = $this->findRoot();
		if ($root) {
			return $root;
		}

		return $this->addRoot();
	}

	/**
	 * @param string $catalogName
	 * @return Category[]
	 */
	public function findByCatalogName($catalogName)
	{
		return $this
			->getFindByCatalogNameQuery($catalogName)
			->getQuery()
			->execute();
	}

	/**
	 * @param int $id
	 * @return Category|null
	 */
	public function findOneById($id)
	{
		return $this->findOneBy(array('id' => $id));
	}

	/**
	 * @param string $catalogName
	 * @return QueryBuilder
	 */
	public function getFindByCatalogNameQuery($catalogName = null)
	{
		$query = $this->createQueryBuilder('c');

		if ($catalogName) {
			$query
				->join('c.catalog', 'cc')
				->where('cc.name = :name')
				->setParameter('name', $catalogName);
		}

		$query
			->orderBy('c.root', 'ASC')
			->addOrderBy('c.left', 'ASC')
			->addOrderBy('c.title', 'ASC');

		return $query;
	}

	/**
	 * @param  Category[] $pages
	 * @param  Category   $parent
	 * @return array
	 */
	private function mapCategories(array $pages, Category $parent = null)
	{
		$res = array();

		if (is_null($parent)) {
			$parent = $this->findRootOrCreate();
		}

		foreach ($pages as $page) {
			if ($page->getParent() === $parent) {
				$res[] = array(
					'title'    => $page->getTitle(),
					'id'       => $page->getId(),
					'key'      => $page->getId(),
					'children' => $this->mapCategories($pages, $page),
				);
			}
		}

		return $res;
	}

	/**
	 * @return Category|null
	 */
	private function findRoot()
	{
		return $this->findOneBy(array('parent' => null));
	}

	/**
	 * @return Category
	 */
	private function addRoot()
	{
		$root = new Category();
		$root->setTitle('ns_catalog_categories_root_category');
		$root->setName('ns_catalog_categories_root_category');

		$this->_em->persist($root);
		$this->_em->flush();

		return $root;
	}
}