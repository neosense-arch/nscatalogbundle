<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use NS\PropertiesBundle\Entity\PropertyType;

/**
 * @ORM\Table(name="ns_catalog_catalogs")
 * @ORM\Entity(repositoryClass="CatalogRepository")
 */
class Catalog
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
	 * @ORM\Column(type="string")
	 */
	private $name;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $formTypeName;

	/**
	 * @var Category[]
	 * @ORM\OneToMany(targetEntity="Category", mappedBy="catalog")
	 */
	private $categories;

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
	 * @param Category[] $categories
	 */
	public function setCategories($categories)
	{
		$this->categories = $categories;
	}

	/**
	 * @return Category[]
	 */
	public function getCategories()
	{
		return $this->categories;
	}

	/**
	 * @param string $formTypeName
	 */
	public function setFormTypeName($formTypeName)
	{
		$this->formTypeName = $formTypeName;
	}

	/**
	 * @return string
	 */
	public function getFormTypeName()
	{
		return $this->formTypeName;
	}
}
