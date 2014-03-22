<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="ns_catalog_types")
 * @ORM\Entity(repositoryClass="TypeRepository")
 */
class Type
{
	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $title;

	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $name;

    /**
     * @var ArrayCollection|TypeElement[]
     * @ORM\OneToMany(targetEntity="TypeElement", mappedBy="type", cascade={"persist", "remove"})
     */
    private $elements;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

    /**
     * @param ArrayCollection|TypeElement[] $elements
     */
    public function setElements($elements)
    {
        $this->elements = $elements;
        foreach ($this->elements as $element) {
            $element->setType($this);
        }
    }

    /**
     * @return ArrayCollection|TypeElement[]
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasElement($name)
    {
        return $this->elements->exists(function(TypeElement $element) use($name){
            return $name === $element->getName();
        });
    }

    /**
     * @param string $name
     * @return TypeElement
     * @throws \Exception
     */
    public function getElement($name)
    {
        foreach ($this->elements as $element) {
            if ($element->getName() === $name) {
                return $element;
            }
        }
        throw new \Exception("Element named '{$name}' wasn't found in type '{$this->getName()}' (#{$this->getId()})");
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
}
