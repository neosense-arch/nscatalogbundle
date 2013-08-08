<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
		$this->value = $value;
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}
}
