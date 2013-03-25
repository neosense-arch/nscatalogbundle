<?php
namespace NS\CatalogBundle\Twig\Extension;

use NS\CatalogBundle\Entity\Item;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class NSCatalogInlineEditExtension
 *
 * @package NS\CatalogBundle\Twig\Extension
 */
class inlineEditExtension extends \Twig_Extension
{
	const TYPE_ITEM_BASE_PROPERTY   = 'property';
	const TYPE_ITEM_CUSTOM_SETTING  = 'setting';

	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
			'ie_boolean' => new \Twig_Function_Method($this, 'ieBoolean', array('is_safe' => array('html'))),
			'ie_price'   => new \Twig_Function_Method($this, 'iePrice',   array('is_safe' => array('html'))),
			'ie_text'    => new \Twig_Function_Method($this, 'ieText',   array('is_safe' => array('html'))),
			'ie_visible' => new \Twig_Function_Method($this, 'ieVisible', array('is_safe' => array('html'))),
		);
    }

	/**
	 * @param Item    $item
	 * @param string  $field
	 * @param boolean $value
	 * @param array   $options
	 * @return string
	 */
	public function ieBoolean(Item $item, $field, $value, $options = array())
    {
		$options = $this->processOptions(array(
			'class' => 'icon-ok',
		), $options);

		/** @var $renderer EngineInterface */
		$renderer = $this->container->get('templating');
		return $renderer->render('NSCatalogBundle:InlineEdit:ie-boolean.html.twig', array(
			'item'    => $item,
			'field'   => $field,
			'value'   => $value,
			'options' => $options,
		));
    }

	/**
	 * @param Item $item
	 * @return string
	 */
	public function ieVisible(Item $item)
    {
		return $this->ieBoolean($item, 'visible', $item->getVisible(), array(
			'class' => 'icon-eye-open',
			'type'  => self::TYPE_ITEM_BASE_PROPERTY,
		));
    }

	/**
	 * @param Item   $item
	 * @param string $field
	 * @param string $value
	 * @param array  $options
	 * @return string
	 */
	public function iePrice(Item $item, $field, $value, $options = array())
    {
		$options = $this->processOptions(array(), $options);

		/** @var $renderer EngineInterface */
		$renderer = $this->container->get('templating');
		return $renderer->render('NSCatalogBundle:InlineEdit:ie-price.html.twig', array(
			'item'    => $item,
			'field'   => $field,
			'value'   => $value,
			'options' => $options,
		));
    }

	/**
	 * @param Item   $item
	 * @param string $field
	 * @param string $value
	 * @param array  $options
	 * @return string
	 */
	public function ieText(Item $item, $field, $value, $options = array())
    {
		$options = $this->processOptions(array(), $options);

		/** @var $renderer EngineInterface */
		$renderer = $this->container->get('templating');
		return $renderer->render('NSCatalogBundle:InlineEdit:ie-text.html.twig', array(
			'item'    => $item,
			'field'   => $field,
			'value'   => $value,
			'options' => $options,
		));
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
		return 'ns_catalog_inline_edit';
    }

	/**
	 * @return array
	 */
	private function getDefaultOptions()
	{
		return array(
			'type' => self::TYPE_ITEM_CUSTOM_SETTING,
			'url'  => null,
		);
	}

	/**
	 * @param array $default
	 * @param array $callerOptions
	 * @return array
	 */
	private function processOptions(array $default, array $callerOptions)
	{
		$options = array_merge(
			$this->getDefaultOptions(),
			$default,
			$callerOptions
		);

		if (empty($options['url'])) {
			$options['url'] = $this->getUrl($options['type']);
		}

		return $options;
	}

	/**
	 * @param  string $type
	 * @return string
	 * @throws \Exception
	 */
	private function getUrl($type)
	{
		$actions = array(
			self::TYPE_ITEM_BASE_PROPERTY  => 'updateBaseProperty',
			self::TYPE_ITEM_CUSTOM_SETTING => 'updateCustomSetting',
		);

		if (empty($actions[$type])) {
			$availableTypes = join("', '", array_keys($actions));
			throw new \Exception("Wrong type '{$type}', use one of '{$availableTypes}'");
		}

		/** @var $router RouterInterface */
		$router = $this->container->get('router');
		return $router->generate('ns_admin_bundle', array(
			'adminBundle'     => 'NSCatalogBundle',
			'adminController' => 'itemsApi',
			'adminAction'     => $actions[$type],
		));
	}
}