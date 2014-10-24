<?php

namespace NS\CatalogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class NodeGeoPointType
 *
 * @package NS\CatalogBundle\Form\Type
 */
class NodeGeoPointType extends AbstractType
{
	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_catalog_node_geo_point';
    }
}
