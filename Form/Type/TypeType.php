<?php

namespace NS\CatalogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TypeType extends AbstractType
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
				'required' => false,
			))
            ->add('elements', 'ns_catalog_type_elements', array(
                'label'    => 'Элементы',
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
            'data_class' => 'NS\CatalogBundle\Entity\Type'
        ));
    }

	/**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_catalog_type';
    }
}
