<?php

namespace NS\CatalogBundle\Menu;

use Knp\Menu\NodeInterface;
use NS\CatalogBundle\Entity\Category;
use Symfony\Component\Routing\RouterInterface;

/**
 * Category node entity
 *
 */
class CategoryNode implements NodeInterface
{
	const CATEGORY_PAGE_ROUTE = 'ns_catalog_category';

	/**
	 * @var Category
	 */
	private $category;

	/**
	 * @var RouterInterface
	 */
	private $router;

	/**
	 * @var string
	 */
	private $routeName = self::CATEGORY_PAGE_ROUTE;

	/**
	 * @param Category        $category
	 * @param RouterInterface $router
	 * @param string          $routeName
	 */
	public function __construct(Category $category, RouterInterface $router, $routeName = null)
	{
		$this->category = $category;
		$this->router = $router;
		if ($routeName) {
			$this->setRouteName($routeName);
		}
	}

	/**
	 * Get the name of the node
	 *
	 * Each child of a node must have a unique name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->category->getId();
	}

	/**
	 * Get the options for the factory to create the item for this node
	 *
	 * @return array
	 */
	public function getOptions()
	{
		return array(
			'label'   => $this->category->getTitle(),
			'display' => true,
			'uri'     => $this->getUrl(),
			'extras'  => array(
				'category' => $this->category,
			),
		);
	}

	/**
	 * Get the child nodes implementing NodeInterface
	 *
	 * @return \Traversable
	 */
	public function getChildren()
	{
		$children = array();
		foreach ($this->category->getChildren() as $category) {
			$children[] = new self($category, $this->router, $this->routeName);
		}
		return $children;
	}
	/**
	 * @param string $routeName
	 */
	public function setRouteName($routeName)
	{
		$this->routeName = $routeName;
	}

	/**
	 * Generates page URL
	 *
	 * @return string
	 */
	private function getUrl()
	{
		return $this->router->generate($this->routeName, array(
			'categorySlug' => $this->category->getSlug(),
		));
	}
}
