<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
	/**
	 * @return array
	 */
	public function findCategoriesForDynatree()
	{
		$categories = $this->findBy(array(), array('root' => 'ASC', 'left' => 'ASC', 'title' => 'ASC'));
		return $this->mapCategories($categories);
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