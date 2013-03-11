<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="ns_catalog_items")
 * @ORM\Entity(repositoryClass="ItemRepository")
 */
class Item
{
	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var Category
	 *
	 * @ORM\ManyToOne(targetEntity="Category", inversedBy="items")
	 * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $category;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $title;

	/**
	 * @var string
	 * @Gedmo\Slug(fields={"title"})
	 * @ORM\Column(length=128, unique=true)
	 */
	private $slug;

	/**
	 * @var string
	 * @ORM\Column(type="text")
	 */
	private $settings;

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
	 * @return string
	 */
	public function getSlug()
	{
		return $this->slug;
	}

	/**
	 * @param Category $category
	 */
	public function setCategory(Category $category)
	{
		$this->category = $category;
	}

	/**
	 * @return Category
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * @param mixed $settings
	 */
	public function setSettings($settings)
	{
		$this->settings = serialize($settings);
	}

	/**
	 * @return mixed
	 */
	public function getSettings()
	{
		return unserialize($this->settings);
	}
}
