<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="ns_catalog_categories")
 * @ORM\Entity(repositoryClass="CategoryRepository")
 * @Gedmo\Tree(type="nested")
 */
class Category
{
	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @Gedmo\TreeLeft
	 * @ORM\Column(name="t_left", type="integer")
	 */
	private $left;

	/**
	 * @var int
	 * @Gedmo\TreeLevel
	 * @ORM\Column(name="t_level", type="integer")
	 */
	private $level;

	/**
	 * @Gedmo\TreeRight
	 * @ORM\Column(name="t_right", type="integer")
	 */
	private $right;

	/**
	 * @Gedmo\TreeRoot
	 * @ORM\Column(name="t_root", type="integer", nullable=true)
	 */
	private $root;

	/**
	 * @var Category
	 *
	 * @Gedmo\TreeParent
	 * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $parent;

	/**
	 * @var Category[]
	 * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
	 * @ORM\OrderBy({"left" = "ASC"})
	 */
	private $children;

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
	 * @var string
	 * @Gedmo\Slug(fields={"title"})
	 * @ORM\Column(length=128, unique=true)
	 */
	private $slug;

	/**
	 * @var Catalog
	 * @ORM\ManyToOne(targetEntity="Catalog", inversedBy="categories")
	 * @ORM\JoinColumn(name="catalog_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $catalog;

	/**
	 * @var Item[]
	 * @ORM\OneToMany(targetEntity="Item", mappedBy="category")
	 */
	private $items;

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
	 * @return string
	 */
	public function getSlug()
	{
		return $this->slug;
	}

	/**
	 * @param Category $parent
	 */
	public function setParent(Category $parent = null)
	{
		$this->parent = $parent;
	}

	/**
	 * @return Category
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * @param Category[] $children
	 */
	public function setChildren($children)
	{
		$this->children = $children;
	}

	/**
	 * @return Category[]
	 */
	public function getChildren()
	{
		return $this->children;
	}

	/**
	 * @return int
	 */
	public function getLevel()
	{
		return $this->level;
	}

	/**
	 * @param \NS\CatalogBundle\Entity\Catalog $catalog
	 */
	public function setCatalog($catalog)
	{
		$this->catalog = $catalog;
	}

	/**
	 * @return \NS\CatalogBundle\Entity\Catalog
	 */
	public function getCatalog()
	{
		return $this->catalog;
	}

	/**
	 * Retrieves options label (for combobox)
	 *
	 * @param  string $levelIndicator
	 * @return string
	 */
	public function getOptionLabel($levelIndicator = '--')
	{
		return str_repeat($levelIndicator, $this->level) . ' ' . $this->getTitle();
	}
}
