<?php
namespace NS\CatalogBundle\Twig\Extension;

use NS\CatalogBundle\Entity\Item;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NSCatalogInlineEditExtension
 *
 * @package NS\CatalogBundle\Twig\Extension
 */
class inlineEditExtension extends \Twig_Extension
{
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
		);
    }

	/**
	 * @param Item   $item
	 * @param string $field
	 * @param array  $options
	 * @return string
	 */
	public function ieBoolean(Item $item, $field, $options = array())
    {
		$options = array_merge(array(
			'class'        => 'icon-ok',
			'opacityTrue'  => '0.7',
			'opacityFalse' => '0.1',
		), $options);

		/** @var $renderer EngineInterface */
		$renderer = $this->container->get('templating');
		return $renderer->render('NSCatalogBundle:InlineEdit:ie-boolean.html.twig', array(
			'item'    => $item,
			'field'   => $field,
			'options' => $options,
		));
    }

	/**
	 * @param Item   $item
	 * @param string $field
	 * @param array  $options
	 * @return string
	 */
	public function iePrice(Item $item, $field, $options = array())
    {
		$options = array_merge(array(
		), $options);

		/** @var $renderer EngineInterface */
		$renderer = $this->container->get('templating');
		return $renderer->render('NSCatalogBundle:InlineEdit:ie-price.html.twig', array(
			'item'    => $item,
			'field'   => $field,
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
}