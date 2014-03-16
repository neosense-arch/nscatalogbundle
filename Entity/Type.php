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
     * @var ArrayCollection|TypeElement[]
     * @ORM\OneToMany(targetEntity="TypeElement", mappedBy="type", cascade={"persist", "remove"})
     */
    private $elements;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
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
}
