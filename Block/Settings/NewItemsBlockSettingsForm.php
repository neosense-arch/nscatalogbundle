<?php

namespace NS\CatalogBundle\Block\Settings;

use NS\AdminBundle\Form\Type\TinyMceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewItemsBlockSettingsForm extends AbstractType
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
			->add('count', 'text', array(
				'label'    => 'Количество товаров',
				'required' => false,
			))
        ;
    }

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NS\CatalogBundle\Block\Settings\NewItemsBlockSettingsModel'
        ));
    }

	/**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_catalogbundle_newitemsblocksettingsform';
    }
}
