<?php

namespace NS\CatalogBundle\Model;

use NS\ShopBundle\Item\Priceable;

/**
 * Temporary legacy-support model to formally implement AbstractSettings interface
 *
 * @package NS\CatalogBundle\Model
 */
class GenericSettings extends AbstractSettings implements Priceable
{
    /**
     * @var array
     */
    private $data = array();

    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __isset($name)
    {
        return true;
    }

    public function toArray()
    {
        return $this->data;
    }

    public function setSetting($name, $value)
    {
        $this->__set($name, $value);
    }

    public function getSetting($name)
    {
        return $this->__get($name);
    }

    /**
     * Retrieves item price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->__get('price');
    }
}