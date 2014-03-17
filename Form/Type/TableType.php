<?php

namespace NS\CatalogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use NS\AdminBundle\Form\DataTransformer\ArrayToJsonTransformer;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Entity\Category;
use NS\CatalogBundle\Entity\Type;
use NS\CatalogBundle\Entity\TypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TableType
 *
 * @package NS\CatalogBundle\Form\Type
 */
class TableType extends AbstractType
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
     * Builds form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @throws \Exception
     */
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $arrayToJsonTransformer = new ArrayToJsonTransformer();
        $builder->addModelTransformer($arrayToJsonTransformer);
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // retrieving type name
        $typeName = $options['type'];
        if (!$typeName) {
            throw new \Exception("Required option 'type' wasn't found");
        }

        // retrieving type
        /** @var Type $type */
        $type = $this->typeRepository->findOneBy(array('name' => $typeName));
        if (!$type) {
            throw new \Exception("Type named '$typeName' wasn't found");
        }

        // retrieving rows elements
        $rows = $options['rows'];
        if (empty($rows) || !is_array($rows)) {
            throw new \Exception("Required option 'rows' must be not empty array");
        }
        $rowsElements = array();
        foreach ($rows as $row) {
            $rowsElements[] = $type->getElement($row);
        }
        $view->vars['rowsElements'] = $rowsElements;
    }

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'type' => null,
            'rows' => null,
        ));
    }

	/**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_catalog_table';
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'hidden';
    }
}
