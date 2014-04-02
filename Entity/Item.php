<?php

namespace NS\CatalogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use NS\CatalogBundle\Model\AbstractSettings;
use NS\CatalogBundle\Model\GenericSettings;
use NS\SearchBundle\Agent\ModelInterface;

/**
 * @ORM\Table(name="ns_catalog_items")
 * @ORM\Entity(repositoryClass="ItemRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Item implements ModelInterface
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
	 * @var boolean
	 * @ORM\Column(type="boolean")
	 */
	private $visible = true;

	/**
	 * @var string
	 * @ORM\OneToMany(targetEntity="Setting", mappedBy="item", cascade={"persist", "remove"})
	 */
	private $rawSettings;

	/**
	 * @var AbstractSettings
	 */
	private $settings;

    /**
     * Constructor
     */
    function __construct()
    {
        $this->rawSettings = new ArrayCollection();
    }

    /**
     * Clone
     */
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
	 * @param boolean $visible
	 */
	public function setVisible($visible)
	{
		$this->visible = $visible;
	}

	/**
	 * @return boolean
	 */
	public function getVisible()
	{
		return $this->visible;
	}

	/**
	 * @param Setting[] $rawSettings
	 */
	public function setRawSettings($rawSettings)
	{
		$this->rawSettings = $rawSettings;
	}

	/**
	 * @return Setting[]|ArrayCollection
	 */
	public function getRawSettings()
	{
		return $this->rawSettings;
	}

	/**
	 * @param string $name
	 * @return Setting
	 */
	public function getRawSetting($name)
	{
		foreach ($this->getRawSettings() as $setting) {
			if ($setting->getName() === $name) {
				return $setting;
			}
		}

		$setting = new Setting();
		$setting->setItem($this);
		$setting->setName($name);

		return $setting;
	}

	/**
	 * @param AbstractSettings $settings
	 */
	public function setSettings(AbstractSettings $settings)
	{
		$rawSettings = array();
		foreach ($settings->toArray() as $key => $value) {
			$setting = $this->getRawSetting($key);
			$setting->setValue($value);
			$rawSettings[] = $setting;
		}
		$this->setRawSettings($rawSettings);

		$this->settings = $settings;
	}

	/**
	 * @return AbstractSettings
	 */
	public function getSettings()
	{
        if (!$this->settings) {
            $this->settings = $this->mapSettings();
        }

		return $this->settings;
	}

	/**
     * Maps settings object
     *
     * @return AbstractSettings
     * @throws \Exception
     */
    private function mapSettings()
	{
		$category = $this->getCategory();
		if (!$category) {
            return null;
		}

        if (!$category->getType()) {
            $catalog = $category->getCatalog();
            if (!$catalog) {
                throw new \Exception("Category #{$category->getId()} has no catalog");
            }
            $modelClassName = $catalog->getSettingsModelClassName();
            $settings = new $modelClassName();
        }
        else {
            $settings = new GenericSettings();
        }

		/** @var $settings AbstractSettings */
		foreach ($this->getRawSettings() as $setting) {
			$settings->setSetting($setting->getName(), $setting->getValue());
		}

		return $settings;
	}

	/**
	 * @return mixed
	 */
	public function getSearchModelId()
	{
		return $this->getId();
	}
}
