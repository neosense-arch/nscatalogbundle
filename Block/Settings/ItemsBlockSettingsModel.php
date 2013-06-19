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
	private $template;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->template = 'NSCatalogBundle:Blocks:itemsBlock.html.twig';
	}

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
	 * @param string $template
	 */
	public function setTemplate($template)
	{
		$this->template = $template;
	}

	/**
	 * @return string
	 */
	public function getTemplate()
	{
		return $this->template;
	}
}
