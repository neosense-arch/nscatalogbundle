<?php

namespace NS\CatalogBundle\Block\Settings;

/**
 * Class SearchBlockSettingsModel
 *
 * @package NS\CatalogBundle\Block\Settings
 */
class SearchBlockSettingsModel
{
	/**
	 * @var string
	 */
	private $settings = '';
	/**
	 * @param string $settings
	 */
	public function setSettings($settings)
	{
		$this->settings = $settings;
	}
	/**
	 * @return string
	 */
	public function getSettings()
	{
		return $this->settings;
	}

	/**
	 * @return string[]
	 */
	public function getSettingsArray()
	{
		$settings = str_replace(array(';', ':', ' '), ',', $this->getSettings());
		$settings = explode(',', $settings);

		$res = array();
		foreach ($settings as $setting) {
			$setting = trim($setting);
			if ($setting) {
				$res[] = $setting;
			}
		}

		return $res;
	}
}
