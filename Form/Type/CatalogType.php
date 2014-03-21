<?php

namespace NS\CatalogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CatalogType
 *
 * @package NS\CatalogBundle\Form\Type
 */
class CatalogType extends AbstractType
{
	/**
	 * Builds form
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder
            ->add('title', 'text', array(
                'label'    => 'Название',
                'required' => true,
            ))
            ->add('name', 'text', array(
                'label'    => 'Имя (лат.)',
                'required' => true,
            ))
            ->add('settingsFormTypeName', 'text', array(
                'label'    => 'Имя СЕРВИСА формы свойств товара',
                'required' => true,
            ))
            ->add('settingsModelClassName', 'text', array(
                'label'    => 'Имя класса свойств товара',
                'required' => true,
            ))
        ;
    }

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NS\CatalogBundle\Entity\Catalog'
        ));
    }

	/**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_catalog_catalog';
    }
}
