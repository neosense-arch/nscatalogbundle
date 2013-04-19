<?php

namespace NS\CatalogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoryType extends AbstractType
{
	/**
	 * @var string
	 */
	private $catalogName;

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
			->add('parent', 'category_select', array(
				'label' => 'Родительская категория',
				'required' => false,
				'catalog_name' => $this->catalogName,
			))
			->add('description', 'tinymce', array(
				'label' => 'Описание',
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
            'data_class' => 'NS\CatalogBundle\Entity\Category'
        ));
    }

	/**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_catalogbundle_categorytype';
    }

	/**
	 * @param string $catalogName
	 */
	public function setCatalogName($catalogName)
	{
		$this->catalogName = $catalogName;
	}
}
