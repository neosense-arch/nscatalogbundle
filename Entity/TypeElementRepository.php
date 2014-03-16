<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class TypeElementRepository
 *
 * @package NS\CatalogBundle\Entity
 */
class TypeElementRepository extends EntityRepository
{
    /**
     * @param $id
     * @return TypeElement
     */
    public function findOrCreate($id)
    {
        /** @var TypeElement|null $element */
        $element = $this->find($id);
        if (!$element) {
            $element = new TypeElement();
        }
        return $element;
    }
} 