<?php

namespace NS\CatalogBundle\Block\Settings;

class ItemsBlockSettingsModel
{
	/**
	 * @var string
	 */
	private $count = 5;

	/**
	 * @var bool
	 */
	private $useCategory = true;

    /**
     * @var int
     */
    private $categoryId;

    /**
     * @var bool
     */
    private $recursive = false;

	/**
	 * @var string
	 */
	private $settingName;

	/**
	 * @var string
	 */
	private $settingValue;

	/**
	 * @var string
	 */
	private $order;

    /**
     * @var boolean
     */
    private $isSortable;

	/**
	 * @param string $count
	 */
	public function setCount($count)
	{
		$this->count = $count;
	}

	/**
	 * @return string
	 */
	public function getCount()
	{
		return $this->count;
	}

	/**
	 * @param boolean $useCategory
	 */
	public function setUseCategory($useCategory)
	{
		$this->useCategory = (bool)$useCategory;
	}

	/**
	 * @return boolean
	 */
	public function getUseCategory()
	{
		return $this->useCategory;
	}

    /**
     * @param int $categoryId
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

	/**
	 * @param string $settingName
	 */
	public function setSettingName($settingName)
	{
		$this->settingName = $settingName;
	}

	/**
	 * @return string
	 */
	public function getSettingName()
	{
		return $this->settingName;
	}

	/**
	 * @param string $settingValue
	 */
	public function setSettingValue($settingValue)
	{
		$this->settingValue = $settingValue;
	}

	/**
	 * @return string
	 */
	public function getSettingValue()
	{
		return $this->settingValue;
	}

    /**
     * Retrieves settings conditions in key-value assoc array format
     * e.g. ['hit' => 1]
     *
     * @return array
     */
    public function getSettingsConditions()
    {
        if (!$this->getSettingName()) {
            return array();
        }
        return array($this->getSettingName() => $this->getSettingValue());
    }

	/**
	 * @param string $order
	 */
	public function setOrder($order)
	{
		$this->order = $order;
	}

	/**
	 * @return string
	 */
	public function getOrder()
	{
		return $this->order;
	}

	/**
	 * @return string|null
	 */
	public function getOrderField()
	{
		return $this->getOrderValue(0);
	}

	/**
	 * @return null
	 */
	public function getOrderDirection()
	{
		return $this->getOrderValue(1, 'asc');
	}

	/**
	 * @return null
	 */
	public function getOrderType()
	{
		return $this->getOrderValue(2, 'string');
	}

    /**
     * @return array
     */
    public function getOrderArray()
    {
        return array(
            array($this->getOrderField(), $this->getOrderDirection(), $this->getOrderType()),
        );
    }

	/**
	 * @param int    $index
	 * @param string $default
	 * @return string|null
	 */
	private function getOrderValue($index, $default = null)
	{
		$order = str_replace(array('  ', ',', ';', ':', '/'), ' ', $this->getOrder());
		$parts = explode(' ', $order);

		return array_key_exists($index, $parts) ? $parts[$index] : $default;
	}

    /**
     * @param boolean $recursive
     */
    public function setRecursive($recursive)
    {
        $this->recursive = $recursive;
    }

    /**
     * @return boolean
     */
    public function getRecursive()
    {
        return $this->recursive;
    }

    /**
     * @param boolean $isSortable
     */
    public function setIsSortable($isSortable)
    {
        $this->isSortable = (bool)$isSortable;
    }

    /**
     * @return boolean
     */
    public function getIsSortable()
    {
        return (bool)$this->isSortable;
    }
}
