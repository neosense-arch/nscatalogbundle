<?php

namespace NS\CatalogBundle\Form\Type;

use NS\CatalogBundle\Entity\Type;
use NS\CatalogBundle\Entity\TypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class NodeType
 *
 * @package NS\CatalogBundle\Form\Type
 */
class NodeType extends AbstractType
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

        // adding fields
        foreach ($type->getElements() as $element) {
            $builder->add($element->getName(), $element->getCategory(), array(
                'label'    => $element->getTitle(),
                'required' => false,
            ));
        }
    }

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'type' => null,
        ));
    }

	/**
	 * @return string
	 */
	public function getName()
    {
        return 'ns_catalog_node';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'hidden';
    }
}
