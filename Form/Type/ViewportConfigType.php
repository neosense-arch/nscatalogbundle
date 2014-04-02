<?php

namespace NS\CatalogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use NS\AdminBundle\Form\DataTransformer\ArrayToJsonTransformer;
use NS\CatalogBundle\Entity\Catalog;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\ArrayToPartsTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ViewportConfigType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('orderElement', 'text', array(
                'label'    => 'Сортировка',
                'required' => false,
            ))
            ->add('elements', 'text', array(
                'label'    => 'Элементы таблицы',
                'required' => false,
            ))
        ;
    }

    /**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_catalog_viewport_config';
    }
}
