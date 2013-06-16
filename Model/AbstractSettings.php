<?php

namespace NS\CatalogBundle\Model;


abstract class AbstractSettings
{
	/**
	 * @throws \Exception
	 * @return array
	 */
	public function toArray()
	{
		$res = array();

		$objectVars = get_object_vars($this);
		if (!$objectVars) {
			$class = get_called_class();
			throw new \Exception("Settings model '{$class}' properties wasn't found. You must use protected access mode for properties");
		}

		foreach (get_object_vars($this) as $key => $value) {
			if ($this->settingExists($key)) {
				$res[$key] = $this->getSetting($key);
			}
		}

		return $res;
	}

	/**
	 * @param array $settings
	 */
	public function fromArray(array $settings)
	{
		foreach ($settings as $name => $value) {
			$this->setSetting($name, $value);
		}
	}

	/**
	 * @param string $name
	 * @return string
	 * @throws \Exception
	 */
	public function getSetting($name)
	{
		$getMethod = 'get' . ucfirst($name);
		if (method_exists($this, $getMethod)) {
			return $this->$getMethod();
		}

		$isMethod = 'is' . ucfirst($name);
		if (method_exists($this, $isMethod)) {
			return $this->$isMethod();
		}

		throw new \Exception(sprintf(
			"Property '%s::%s' wasn't found (methods %s, %s wasn't found)",
			get_class($this),
			$name,
			$getMethod,
			$isMethod
		));
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 */
	public function setSetting($name, $value)
	{
		$method = 'set' . ucfirst($name);
		if (method_exists($this, $method)) {
			$this->$method($value);
		}
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function settingExists($name)
	{
		return property_exists($this, $name)
			&& (method_exists($this, 'get' . ucfirst($name))
				|| method_exists($this, 'is' . ucfirst($name)));
	}

	/**
	 * @return string
	 */
	abstract public function getSearchDocumentContents();

	/**
	 * @return string
	 */
	abstract public function getSearchDocumentDescription();
}
