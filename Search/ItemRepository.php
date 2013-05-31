<?php

namespace NS\CatalogBundle\Search;

use NS\CatalogBundle\Entity\ItemRepository as CatalogItemRepository;
use NS\SearchBundle\Agent\RepositoryInterface;
use NS\SearchBundle\Models\ModelCollection;

/**
 * Class ItemRepository
 * @package NS\CatalogBundle\Search
 */
class ItemRepository implements RepositoryInterface
{
	const ITEM_BLOCK_TYPE_NAME = 'NSCatalogBundle:Blocks:itemBlock';

	/**
	 * @var CatalogItemRepository
	 */
	private $itemRepository;

	/**
	 * @param CatalogItemRepository $itemRepository
	 */
	public function __construct(CatalogItemRepository $itemRepository)
	{
		$this->setItemRepository($itemRepository);
	}

	/**
	 * Retrieves all models
	 *
	 * @return ModelCollection
	 */
	public function findAllModels()
	{
		$items = $this
			->getItemRepository()
			->findAll();

		return new ModelCollection($items);
	}

	/**
	 * Retrieves models by ID array
	 *
	 * @param  int[] $ids
	 * @return ModelCollection
	 */
	public function findModelsByIds(array $ids)
	{
		$items = $this
			->getItemRepository()
			->findByIds($ids);

		return new ModelCollection($items);
	}

	/**
	 * @param CatalogItemRepository $blockRepository
	 */
	private function setItemRepository(CatalogItemRepository $blockRepository)
	{
		$this->itemRepository = $blockRepository;
	}

	/**
	 * @return CatalogItemRepository
	 */
	private function getItemRepository()
	{
		return $this->itemRepository;
	}
}
