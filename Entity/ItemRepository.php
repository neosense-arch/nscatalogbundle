<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\ORM\EntityRepository;

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
		return $this->findBy(array(), array('id' => 'DESC'));
	}

	/**
	 * @param Category $category
	 * @return Item[]
	 */
	public function findByCategory(Category $category = null)
	{
		return $this->findBy(array('category' => $category), array('id' => 'DESC'));
	}
}