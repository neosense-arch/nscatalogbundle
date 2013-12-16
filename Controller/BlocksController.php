<?php

namespace NS\CatalogBundle\Controller;

use Doctrine\ORM\Query;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use NS\CatalogBundle\Block\Settings\CategoriesBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\CategoriesMenuBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\CategoryBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\ItemBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\ItemsBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\NewItemsBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\SearchBlockSettingsModel;
use NS\CatalogBundle\Entity\Category;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Menu\CategoryNode;
use NS\CatalogBundle\Menu\Matcher\Voter\CategoryVoter;
use NS\CatalogBundle\QueryBuilder\ItemQueryBuilder;
use NS\CatalogBundle\Service\ItemService;
use NS\CmsBundle\Block\Settings\Generic\CountBlockSettingsModel;
use NS\CmsBundle\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use NS\CmsBundle\Entity\Block;
use NS\CmsBundle\Manager\BlockManager;

use NS\CatalogBundle\Block\Settings\MainItemsBlockSettingsModel;
use NS\CatalogBundle\Entity\ItemRepository;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class BlocksController
 * @package NS\CatalogBundle\Controller
 */
class BlocksController extends Controller
{
	/**
	 * Main items block
	 *
	 * @param  Block $block
	 * @return Response
	 */
	public function mainItemsBlockAction(Block $block)
	{
		/** @var $settings MainItemsBlockSettingsModel */
		$settings = $this
			->getBlockManager()
			->getBlockSettings($block);

		$itemRepository = $this->getItemRepository();

		return $this->render('NSCatalogBundle:Blocks:itemsBlock.html.twig', array(
			'block'    => $block,
			'settings' => $settings,
			'items'    => $itemRepository->findVisibleBySettings('main', true, $settings->getCount())
		));
	}

	/**
	 * New items block
	 *
	 * @param  Block $block
	 * @return Response
	 */
	public function newItemsBlockAction(Block $block)
	{
		/** @var $settings NewItemsBlockSettingsModel */
		$settings = $this
			->getBlockManager()
			->getBlockSettings($block);

		$pagination = $this->createPagination(
			$this->getItemRepository()->getFindVisibleBySettingsQuery('new', true),
			$settings->getCount()
		);

		return $this->render('NSCatalogBundle:Blocks:itemsBlock.html.twig', array(
			'block'      => $block,
			'settings'   => $settings,
			'items'      => $pagination,
			'pagination' => $pagination,
		));
	}

	/**
	 * Categories menu block
	 *
	 * @param  Block $block
	 * @return Response
	 */
	public function categoriesMenuBlockAction(Block $block)
	{
		/** @var $settings CategoriesMenuBlockSettingsModel */
		$settings = $this->getBlockManager()->getBlockSettings($block);

		/** @var $categoryRepository CategoryRepository */
		$categoryRepository = $this->getDoctrine()->getManager()->getRepository('NSCatalogBundle:Category');

		/** @var $router RouterInterface */
		$router = $this->get('router');

		// current category
		$slug = $this->getRequest()->attributes->get('categorySlug');
		$currentCategory = $categoryRepository->findOneBySlug($slug);

		// retrieving root category
		if ($settings->getIsSubmenu() && $currentCategory) {
			$rootCategory = $currentCategory;
			while ($rootCategory->getParent() && $rootCategory->getParent()->getParent()){
				$rootCategory = $rootCategory->getParent();
			}
		}
		else {
			$rootCategory = $categoryRepository->findRootOrCreate();
		}

		// creating from root node
		$factory = new MenuFactory();
		$rootNode = new CategoryNode($rootCategory, $router, $settings->getRouteName());
		$menu = $factory->createFromNode($rootNode);

		$matcher = new Matcher();
		if ($currentCategory) {
			$matcher->addVoter(new CategoryVoter($currentCategory));
		}

		// items
		$items = $menu->getChildren();

		// sorting items
		/** @var ItemInterface[] $sorted */
		$sorted = array();
		$sortItems = explode(',', $settings->getSortOrder());
		foreach ($sortItems as $slug) {
			foreach ($items as $item) {
				/** @var Category $currentCategory */
				$category = $item->getExtra('category');
				if ($category->getSlug() === $slug) {
					$sorted[] = $item;
					break;
				}
			}
		}

		foreach ($items as $item) {
			/** @var Category $currentCategory */
			$category = $item->getExtra('category');
			if (!in_array($category->getSlug(), $sortItems)) {
				$sorted[] = $item;
			}
		}
		$menu->setChildren($sorted);

		// checking active items
		$hasActiveItems = false;
		if ($currentCategory) {
			foreach ($sorted as $item) {
				if ($matcher->isCurrent($item) || $matcher->isAncestor($item)) {
					$hasActiveItems = true;
					break;
				}
			}
		}

		// checking automatic redirect option
		if (!$hasActiveItems && $settings->getRedirectToFirstItem() && count($sorted)) {
			return $this->redirect(
				$this->generateUrl(
					$settings->getRouteName(),
					array(
						'categorySlug' => $sorted[0]->getExtra('category')->getSlug(),
					)
				)
			);
		}

		return $this->render($settings->getTemplate(), array(
			'block'           => $block,
			'settings'        => $settings,
			'menu'            => $menu,
			'matcher'         => $matcher,
			'currentCategory' => $currentCategory,
		));
	}

	/**
	 * Categories block
	 *
	 * @param  Block $block
	 * @return Response
	 */
	public function categoriesBlockAction(Block $block)
	{
		/** @var $settings CategoriesBlockSettingsModel */
		$settings = $this->getBlockManager()->getBlockSettings($block);

		/** @var $categoryRepository CategoryRepository */
		$categoryRepository = $this->getDoctrine()->getManager()->getRepository('NSCatalogBundle:Category');

		$categorySlug = $this->getRequest()->attributes->get('categorySlug');
		$category = $categoryRepository->findOneBySlug($categorySlug);

		$categories = $categoryRepository->findByCategory($category);

		return $this->render('NSCatalogBundle:Blocks:categoriesBlock.html.twig', array(
			'block'      => $block,
			'settings'   => $settings,
			'categories' => $categories,
		));
	}

	/**
	 * Category block
	 *
	 * @param  Block $block
	 * @return Response
	 */
	public function categoryBlockAction(Block $block)
	{
		/** @var $settings CategoryBlockSettingsModel */
		$settings = $this
			->getBlockManager()
			->getBlockSettings($block);

		/** @var $categoryRepository CategoryRepository */
		$categoryRepository = $this->getDoctrine()->getManager()->getRepository('NSCatalogBundle:Category');

		$categorySlug = $this->getRequest()->attributes->get('categorySlug');
		$category = $categoryRepository->findOneBySlug($categorySlug);

		if (!$category) {
			return Response::create('', 404);
		}

		return $this->render($block->getTemplate('NSCatalogBundle:Blocks:categoryBlock.html.twig'), array(
			'block'          => $block,
			'settings'       => $settings,
			'category'       => $category,
			'rootCategories' => $categoryRepository->findRootOrCreate()->getChildren(),
		));
	}

	/**
	 * Category items block
	 *
	 * @param  Block $block
	 * @return Response
	 */
	public function itemsBlockAction(Block $block)
	{
		/** @var $settings ItemsBlockSettingsModel */
		$settings = $this
			->getBlockManager()
			->getBlockSettings($block);

		// creating query builder
		$queryBuilder = $this
			->getItemService()
			->createItemQueryBuilder()
			->andVisible();

		// filtering by category
		$category = $this->getRequestCategory();
		if ($settings->getUseCategory() && $category) {
			$queryBuilder->andWhereCategory($category);
		}

		// filtering by settings
		elseif ($settings->getSettingName()) {
			$queryBuilder->andWhereSetting($settings->getSettingName(), $settings->getSettingValue());
		}

		// ordering
		if ($settings->getOrder()) {
			$queryBuilder->orderBySetting(
				$settings->getOrderField(),
				$settings->getOrderDirection(),
				$settings->getOrderType()
			);
		}

		// creating pagination
		$pagination = $this->createPagination(
			$queryBuilder->getQuery(),
			$settings->getCount()
		);

		return $this->render($settings->getTemplate(), array(
			'block'      => $block,
			'settings'   => $settings,
			'items'      => $pagination,
			'pagination' => $pagination,
			'category'   => $category,
		));
	}

	/**
	 * Item detail info block
	 *
	 * @param  Block $block
	 * @return Response
	 */
	public function itemBlockAction(Block $block)
	{
		/** @var $settings ItemBlockSettingsModel */
		$settings = $this->getBlockManager()->getBlockSettings($block);

		/** @var $categoryRepository CategoryRepository */
		$categoryRepository = $this->getDoctrine()->getManager()->getRepository('NSCatalogBundle:Category');

		$categorySlug = $this->getRequest()->attributes->get('categorySlug');
		$category = $categoryRepository->findOneBySlug($categorySlug);

		/** @var $itemRepository ItemRepository */
		$itemRepository = $this->getDoctrine()->getManager()->getRepository('NSCatalogBundle:Item');
		$item = $itemRepository->findOneBySlug($this->getRequest()->attributes->get('itemSlug'));

		if (!$item) {
			return Response::create('', 404);
		}

		if ($item->getCategory() !== $category) {
			$item = null;
		}

		return $this->render('NSCatalogBundle:Blocks:itemBlock.html.twig', array(
			'block'      => $block,
			'settings'   => $settings,
			'item'       => $item,
		));
	}

	/**
	 * @param  Block $block
	 * @return Response
	 */
	public function fullListBlockAction(Block $block)
	{
		/** @var $settings CountBlockSettingsModel */
		$settings = $this
			->getBlockManager()
			->getBlockSettings($block);

		$pagination = $this->createPagination(
			$this->getItemRepository()->getFindFullCatalogQuery(),
			$settings->getCount()
		);

		return $this->render('NSCatalogBundle:Blocks:fullListBlock.html.twig', array(
			'block'      => $block,
			'settings'   => $settings,
			'items'      => $pagination,
			'pagination' => $pagination,
		));
	}

	/**
	 * @param  Block $block
	 * @return Response
	 */
	public function searchBlockAction(Block $block)
	{
		/** @var $settings SearchBlockSettingsModel */
		$settings = $this
			->getBlockManager()
			->getBlockSettings($block);

		$itemService = $this->getItemService();
		$items = $itemService->search(
			$this->getRequest()->query->get('query'),
			$settings->getSettingsArray(),
			30
		);

		return $this->render('NSCatalogBundle:Blocks:searchBlock.html.twig', array(
			'block'      => $block,
			'settings'   => $settings,
			'items'      => $items,
			'query'      => $this->getRequest()->query->get('query'),
		));
	}

	/**
	 * @return BlockManager
	 */
	private function getBlockManager()
	{
		return $this->container->get('ns_cms.manager.block');
	}

	/**
	 * @return ItemRepository
	 */
	private function getItemRepository()
	{
		return $this->get('ns_catalog.repository.item');
	}

	/**
	 * @return CategoryRepository
	 */
	private function getCategoryRepository()
	{
		return $this->get('ns_catalog.repository.category');
	}

	/**
	 * @return ItemService
	 */
	private function getItemService()
	{
		return $this->get('ns_catalog.service.item');
	}

	/**
	 * @return Category|null
	 */
	private function getRequestCategory()
	{
		$categorySlug = $this
			->getRequest()
			->attributes
			->get('categorySlug');

		return $this
			->getCategoryRepository()
			->findOneBySlug($categorySlug);
	}

	/**
	 * @param Query $query
	 * @param int   $itemsPerPage
	 * @return PaginationInterface
	 */
	private function createPagination(Query $query, $itemsPerPage)
	{
		return $this->get('knp_paginator')->paginate(
			$query,
			$this->getRequest()->query->get('page', 1),
			$itemsPerPage
		);
	}
}
