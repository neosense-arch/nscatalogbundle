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
        if (!$options['type']) {
            throw new \Exception("Required option 'type' wasn't found");
        }

        $type = $this->getType($options['type']);

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
     * @param int|string|Type $optionsType
     * @return Type
     * @throws \Exception
     */
    private function getType($optionsType)
    {
        // type by instance
        if ($optionsType instanceof Type) {
            return $optionsType;
        }

        // type by id
        $typeId = (int)$optionsType;
        if ($typeId) {
            $type = $this->typeRepository->find($typeId);
            if (!$type) {
                throw new \Exception("Type #{$typeId}' wasn't found");
            }
            return $type;
        }

        // type by name
        $type = $this->typeRepository->findOneBy(array('name' => $optionsType));
        if (!$type) {
            throw new \Exception("Type named '{$optionsType}' wasn't found");
        }
        return $type;
    }
}
