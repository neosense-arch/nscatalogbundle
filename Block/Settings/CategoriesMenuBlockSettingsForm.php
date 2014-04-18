<?php

namespace NS\CatalogBundle\Block\Settings;

use NS\AdminBundle\Form\Type\TinyMceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoriesMenuBlockSettingsForm extends AbstractType
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
            ->add('categoryId', 'category_select', array(
                'label'       => 'Категория',
                'required'    => false,
                'id_only'     => true,
                'empty_value' => '[ Не выбрано ]',
            ))
			->add('sortOrder', 'text', array(
				'label'    => 'Сортировка',
				'required' => false,
			))
			->add('isSubmenu', 'checkbox', array(
				'label'    => 'Субменю',
				'required' => false,
			))
			->add('routeName', 'text', array(
				'label'    => 'Имя машршута страницы категории',
				'required' => false,
			))
			->add('redirectToFirstItem', 'checkbox', array(
				'label'    => 'Переходить на первую категорию автоматически',
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
            'data_class' => 'NS\CatalogBundle\Block\Settings\CategoriesMenuBlockSettingsModel'
        ));
    }

	/**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_catalogbundle_categoriesmenublocksettingsform';
    }
}
