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
 * Class NodeDateType
 *
 * @package NS\CatalogBundle\Form\Type
 */
class NodeDateType extends AbstractType
{
	/**
	 * Builds form
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            'attr'   => array(
                'class' => 'date'
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'date';
    }

    /**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_catalog_node_date';
    }
}
