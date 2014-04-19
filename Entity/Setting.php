<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use NS\AdminBundle\Form\DataTransformer\ArrayToStringTransformer;
use NS\AdminBundle\Form\DataTransformer\BooleanToStringTransformer;
use NS\AdminBundle\Form\DataTransformer\EntityToIdTransformer;
use NS\AdminBundle\Form\DataTransformer\IdToEntityTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

/**
 * @ORM\Table(name="ns_catalog_settings")
 * @ORM\Entity(repositoryClass="SettingRepository")
 */
class Setting
{
	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var Item
	 *
	 * @ORM\ManyToOne(targetEntity="Item", inversedBy="rawSettings")
	 * @ORM\JoinColumn(name="item_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $item;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $name;

	/**
	 * @var string
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $value;

	public function __clone()
	{
		$this->id = null;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param \NS\CatalogBundle\Entity\Item $item
	 */
	public function setItem($item)
	{
		$this->item = $item;
	}

	/**
	 * @return \NS\CatalogBundle\Entity\Item
	 */
	public function getItem()
	{
		return $this->item;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $value
	 */
	public function setValue($value)
	{
        $transformer = $this->getTransformer();
		$this->value = $transformer ? $transformer->transform($value): $value;
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
        $transformer = $this->getTransformer();
        return $transformer ? $transformer->reverseTransform($this->value): $this->value;
	}

    /**
     * @return DataTransformerInterface|null
     */
    private function getTransformer()
    {
        $element = $this->getTypeElement();
        if ($element) {
            // dirty hack
            global $kernel;
            if ('AppCache' == get_class($kernel)) {
                $kernel = $kernel->getKernel();
            }
            $itemRepository = $kernel->getContainer()->get('ns_catalog.repository.item');

            $transformers = array(
                'ns_catalog_node_date'    => new DateTimeToStringTransformer(),
                'ns_catalog_node_gallery' => new ArrayToStringTransformer(),
                'checkbox'                => new BooleanToStringTransformer(),
                'ns_catalog_node_select'  => new EntityToIdTransformer($itemRepository),
            );
            if (isset($transformers[$element->getCategory()])) {
                return $transformers[$element->getCategory()];
            }
        }
        return null;
    }

    /**
     * @return TypeElement|null
     */
    private function getTypeElement()
    {
        $type = $this->getType();
        if ($type && $type->hasElement($this->name)) {
            return $type->getElement($this->name);
        }
        return null;
    }

    /**
     * @return Type|null
     */
    private function getType()
    {
        if ($this->getItem() && $this->getItem()->getCategory()) {
            return $this->getItem()->getCategory()->getType();
        }
        return null;
    }
}
