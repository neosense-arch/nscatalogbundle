<?php

namespace NS\CatalogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use NS\AdminBundle\Form\DataTransformer\IdToEntityTransformer;
use NS\CatalogBundle\Entity\Catalog;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Entity\Category;
use NS\CatalogBundle\Entity\TypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TypeSelectType
 *
 * @package NS\CatalogBundle\Form\Type
 */
class TypeSelectType extends AbstractType
{
	/**
	 * @var TypeRepository
	 */
	private $typeRepository;

    /**
     * @param TypeRepository $typeRepository
     */
    public function __construct(TypeRepository $typeRepository)
    {
        $this->typeRepository = $typeRepository;
    }

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
		$resolver->setDefaults(array(
            'label' => 'Тип данных',
            'class'    => 'NS\CatalogBundle\Entity\Type',
		));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new IdToEntityTransformer($this->typeRepository));
    }

    /**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_catalog_type_select';
    }

	public function getParent()
	{
		return 'entity';
	}
}
