<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CatalogRepository extends EntityRepository
{
	/**
	 * @param string $name
	 * @return Catalog|null
	 */
	public function findOneByName($name)
	{
		return $this->findOneBy(array('name' => $name));
	}

	/**
	 * @param int $id
	 * @return Catalog|null
	 */
	public function findOneById($id)
	{
		return $this->findOneBy(array('id' => $id));
	}
}