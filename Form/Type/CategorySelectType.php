<?php

namespace NS\CatalogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategorySelectType extends AbstractType
{
	/**
	 * @var CategoryRepository
	 */
	private $categoryRepository;

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
		$queryBuilder = function(Options $options){
			return function(CategoryRepository $er) use($options) {
				return $er->getFindByCatalogNameQuery($options['catalog_name']);
			};
		};

		$resolver->setDefaults(array(
			'class'         => 'NSCatalogBundle:Category',
			'query_builder' => $queryBuilder,
			'property'      => 'optionLabel',
			'empty_value'   => '[ Не выбрано ]',
			'catalog_name'  => null,
		));
    }

	/**
	 * @return string
	 */
	public function getName()
    {
        return 'category_select';
    }

	public function getParent()
	{
		return 'entity';
	}

	/**
	 * @param CategoryRepository $categoryRepository
	 */
	public function setCategoryRepository(CategoryRepository $categoryRepository)
	{
		$this->categoryRepository = $categoryRepository;
	}
}
