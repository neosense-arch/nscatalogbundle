<?php

namespace NS\CatalogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ItemType extends AbstractType
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
			->add('category', 'category_select', array(
				'label' => 'Категория',
				'required' => true,
				'catalog_name' => $this->catalogName,
			))
        ;
    }

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NS\CatalogBundle\Entity\Item'
        ));
    }

	/**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_catalogbundle_itemtype';
    }

	/**
	 * @param string $catalogName
	 */
	public function setCatalogName($catalogName)
	{
		$this->catalogName = $catalogName;
	}
}
