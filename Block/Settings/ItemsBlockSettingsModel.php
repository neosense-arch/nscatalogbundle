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
	 * @var string
	 */
	private $order;

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
		$order = $this->mapOrder();
		if (count($order)) {
			return $order[0];
		}
		return null;
	}

	/**
	 * @return null
	 */
	public function getOrderDirection()
	{
		$order = $this->mapOrder();
		if (count($order) > 1) {
			$map = array(
				'asc'  => SORT_ASC,
				'desc' => SORT_DESC,
			);
			if (!empty($map[$order[1]])) {
				return $map[$order[1]];
			}
		}
		return SORT_ASC;
	}

	/**
	 * @return null
	 */
	public function getOrderType()
	{
		$order = $this->mapOrder();
		if (count($order) > 2) {
			$map = array(
				'numeric' => SORT_NUMERIC,
				'string'  => SORT_STRING,
			);
			if (!empty($map[$order[2]])) {
				return $map[$order[2]];
			}
		}
		return SORT_STRING;
	}

	/**
	 * @return string[]
	 */
	private function mapOrder()
	{
		$order = str_replace(array('  ', ',', ';', ':', '/'), ' ', $this->getOrder());
		return explode(' ', $order);
	}
}
