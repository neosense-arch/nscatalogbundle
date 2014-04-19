<?php

namespace NS\CatalogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use NS\CatalogBundle\Entity\Catalog;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Entity\Category;
use NS\CatalogBundle\Entity\TypeElementRepository;
use NS\CatalogBundle\Form\DataTransformer\TypeElementsToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TypeElementOptionsNodeSelectType extends AbstractType
{
    /**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_catalog_type_element_options_node_select';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('categoryId', 'category_select', array(
                'label'    => 'Категория каталога',
                'required' => true,
            ))
        ;
    }
}
