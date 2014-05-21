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
use Symfony\Component\Routing\RouterInterface;

/**
 * Class TypeSelectType
 *
 * @package NS\CatalogBundle\Form\Type
 */
class TypeChoiceType extends AbstractType
{
	/**
	 * @var TypeRepository
	 */
	private $typeRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param TypeRepository  $typeRepository
     * @param RouterInterface $router
     */
    public function __construct(TypeRepository $typeRepository, RouterInterface $router)
    {
        $this->typeRepository = $typeRepository;
        $this->router         = $router;
    }

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $url = $this->router->generate('ns_admin_bundle', array(
            'adminBundle'     => 'NSCatalogBundle',
            'adminController' => 'types',
            'adminAction'     => 'formAjax',
        ));


        $resolver->setDefaults(array(
            'label'        => 'Тип данных',
            'class'        => 'NS\CatalogBundle\Entity\Type',
            'url'          => $url,
            'dialogWidth'  => 900,
            'dialogHeight' => 600,
		));
    }

    /**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_catalog_type_choice';
    }

	public function getParent()
	{
		return 'ns_entity_add';
	}
}
