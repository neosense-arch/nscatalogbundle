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
	 * @var CategoryRepository
	 */
	private $categoryRepository;

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
		$catalogName = $this->catalogName;
		$queryBuilder = function(CategoryRepository $er) use($catalogName) {
			return $er->getFindByCatalogNameQuery($catalogName);
		};

		$builder
			->add('title', 'text', array(
				'label'    => 'Название',
				'required' => true,
			))
			->add('parent', 'entity', array(
				'label' => 'Родительская категория',
				'required' => false,
				'class' => 'NSCatalogBundle:Category',
				'query_builder' => $queryBuilder,
				'property' => 'optionLabel',
				'empty_value' => '[ Не выбрано ]',
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
	 * @param CategoryRepository $categoryRepository
	 */
	public function setCategoryRepository(CategoryRepository $categoryRepository)
	{
		$this->categoryRepository = $categoryRepository;
	}

	/**
	 * @param string $catalogName
	 */
	public function setCatalogName($catalogName)
	{
		$this->catalogName = $catalogName;
	}
}
