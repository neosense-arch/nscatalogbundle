<?php

namespace NS\CatalogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use NS\CatalogBundle\Entity\Catalog;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Entity\Category;
use NS\CatalogBundle\Entity\ItemRepository;
use NS\CatalogBundle\Service\CatalogService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NodeSelectType extends AbstractType
{
    /**
     * @var CatalogService
     */
    private $catalogService;

    /**
     * @param CatalogService $catalogService
     */
    public function __construct(CatalogService $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    /**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
		$queryBuilder = function(Options $options){
			return function(ItemRepository $ir) use($options) {
				return $ir->createQueryBuilder('i')
                    ->select('i, c')
                    ->join('i.category', 'c')
                    ->where('c.id = :categoryId')
                    ->setParameter('categoryId', $options['categoryId'])
                    ->orderBy('i.title');
			};
		};

		$resolver->setDefaults(array(
            'class'         => 'NSCatalogBundle:Item',
            'query_builder' => $queryBuilder,
            'property'      => 'title',
            'categoryId'    => null,
		));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['categoryId']) {
            throw new \Exception("Required param 'categoryId' wasn't found");
        }

        $category = $this->catalogService->getCategory($options['categoryId']);
        if (!$category) {
            throw new \Exception("Category #{$options['categoryId']} wasn't found");
        }
    }

    /**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_catalog_node_select';
    }

	public function getParent()
	{
		return 'entity';
	}
}
