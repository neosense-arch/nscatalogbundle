<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class SettingRepository extends EntityRepository
{
	/**
	 * @param  int $id
	 * @return Setting
	 */
	public function findOneById($id)
	{
		return $this->findOneBy(array('id' => $id));
	}

	/**
	 * @param Item $item
	 * @return Setting[]
	 */
	public function findByItem(Item $item)
	{
		return $this->findBy(array('item' => $item));
	}
}