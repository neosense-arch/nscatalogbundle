<?php

namespace NS\CatalogBundle\Block\Settings;

use NS\AdminBundle\Form\Type\TinyMceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ItemsBlockSettingsForm extends AbstractType
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
			->add('useCategory', 'checkbox', array(
				'label'    => 'Реагировать на текущую категорию',
				'required' => false,
			))
			->add('settingName', 'text', array(
				'label'    => 'Имя свойства',
				'required' => false,
			))
			->add('settingValue', 'text', array(
				'label'    => 'Значение свойства',
				'required' => false,
			))
			->add('order', 'text', array(
				'label'    => 'Сортировка',
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
            'data_class' => 'NS\CatalogBundle\Block\Settings\ItemsBlockSettingsModel'
        ));
    }

	/**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_catalogbundle_itemsblocksettingsform';
    }
}
