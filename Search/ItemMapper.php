<?php

namespace NS\CatalogBundle\Search;

use NS\CatalogBundle\Entity\Item;
use NS\CmsBundle\Entity\Block;
use NS\SearchBundle\Agent\MapperInterface;
use NS\SearchBundle\Models\Document;
use NS\SearchBundle\Models\DocumentView;
use Symfony\Component\Routing\Router;

/**
 * Class ContentMapper
 *
 * @package NS\CatalogBundle\Search
 */
class ItemMapper implements MapperInterface
{
	/**
	 * @var Router
	 */
	private $router;

	/**
	 * @param Router $router
	 */
	public function __construct(Router $router)
	{
		$this->router = $router;
	}

	/**
	 * Retrieves document by model
	 *
	 * @param  Item $item
	 * @return Document|null
	 */
	public function getDocumentByModel($item)
	{
		return new Document(
			$item->getId(),
			'ns_catalog:item',
			$item->getTitle(),
			$item->getSettings()->getSearchDocumentContents()
		);
	}

	/**
	 * Retrieves document view by model
	 *
	 * @param  Item $item
	 * @return DocumentView
	 */
	public function getDocumentViewByModel($item)
	{
		return new DocumentView(
			$item->getTitle(),
			$item->getSettings()->getSearchDocumentContents(),
			null,
			$this->router->generate('ns_catalog_item', array(
				'categorySlug' => $item->getCategory()->getSlug(),
				'itemSlug'     => $item->getSlug(),
			))
		);
	}
}
