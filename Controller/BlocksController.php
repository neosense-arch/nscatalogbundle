<?php

namespace NS\CatalogBundle\Controller;

use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use NS\CatalogBundle\Block\Settings\CategoriesBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\CategoriesMenuBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\CategoryBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\ItemBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\ItemsBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\NewItemsBlockSettingsModel;
use NS\CatalogBundle\Entity\Category;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Entity\Item;
use NS\CatalogBundle\Menu\CategoryNode;
use NS\CatalogBundle\Menu\Matcher\Voter\CategoryVoter;
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
		$settings = $this->getBlockManager()->getBlockSettings($block);

		/** @var $itemRepository ItemRepository */
		$itemRepository = $this->getDoctrine()->getManager()->getRepository('NSCatalogBundle:Item');

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
		$settings = $this->getBlockManager()->getBlockSettings($block);

		/** @var $itemRepository ItemRepository */
		$itemRepository = $this->getDoctrine()->getManager()->getRepository('NSCatalogBundle:Item');

		// items with pagination
		$query = $itemRepository->getFindVisibleBySettingsQuery('new', true);

		$pagination = $this->get('knp_paginator')->paginate(
			$query,
			(!empty($_GET['page']) ? $_GET['page'] : 1),
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

		// creating from root node
		$factory = new MenuFactory();
		$rootNode = new CategoryNode($categoryRepository->findRootOrCreate(), $router);
		$menu = $factory->createFromNode($rootNode);

		$slug = $this->getRequest()->attributes->get('categorySlug');
		$category = $categoryRepository->findOneBySlug($slug);
		$matcher = new Matcher();
		if ($category) {
			$matcher->addVoter(new CategoryVoter($category));
		}

		// items
		$items = $menu->getChildren();

		// sorting items
		$sorted = array();
		$sortItems = explode(',', $settings->getSortOrder());
		foreach ($sortItems as $slug) {
			foreach ($items as $item) {
				/** @var Category $category */
				$category = $item->getExtra('category');
				if ($category->getSlug() === $slug) {
					$sorted[] = $item;
					break;
				}
			}
		}

		foreach ($items as $item) {
			/** @var Category $category */
			$category = $item->getExtra('category');
			if (!in_array($category->getSlug(), $sortItems)) {
				$sorted[] = $item;
			}
		}

		$menu->setChildren($sorted);

		return $this->render('NSCatalogBundle:Blocks:categoriesMenuBlock.html.twig', array(
			'block'    => $block,
			'settings' => $settings,
			'menu'     => $menu,
			'matcher'  => $matcher,
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
		$settings = $this->getBlockManager()->getBlockSettings($block);

		/** @var $categoryRepository CategoryRepository */
		$categoryRepository = $this->getDoctrine()->getManager()->getRepository('NSCatalogBundle:Category');

		$categorySlug = $this->getRequest()->attributes->get('categorySlug');
		$category = $categoryRepository->findOneBySlug($categorySlug);

		return $this->render('NSCatalogBundle:Blocks:categoryBlock.html.twig', array(
			'block'    => $block,
			'settings' => $settings,
			'category' => $category,
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
		$settings = $this->getBlockManager()->getBlockSettings($block);

		/** @var $categoryRepository CategoryRepository */
		$categoryRepository = $this->getDoctrine()->getManager()->getRepository('NSCatalogBundle:Category');

		$slug = $this->getRequest()->attributes->get('categorySlug');
		$category = $categoryRepository->findOneBySlug($slug);

		/** @var $itemRepository ItemRepository */
		$itemRepository = $this->getDoctrine()->getManager()->getRepository('NSCatalogBundle:Item');

		if ($settings->getUseCategory()) {
			$query = $itemRepository
				->getFindVisibleByCategoryQuery($category);
		}
		else {
			$query = $itemRepository
				->getFindVisibleBySettingsQuery(
					$settings->getSettingName(),
					$settings->getSettingValue()
				);
		}

		$pagination = $this->get('knp_paginator')->paginate(
			$query,
			(!empty($_GET['page']) ? $_GET['page'] : 1),
			$settings->getCount()
		);

		return $this->render('NSCatalogBundle:Blocks:itemsBlock.html.twig', array(
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
		$settings = $this->getBlockManager()->getBlockSettings($block);

		/** @var $itemRepository ItemRepository */
		$itemRepository = $this->getDoctrine()->getManager()->getRepository('NSCatalogBundle:Item');
		$query = $itemRepository->getFindFullCatalogQuery();

		$pagination = $this->get('knp_paginator')->paginate(
			$query,
			(!empty($_GET['page']) ? $_GET['page'] : 1),
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
	 * @return BlockManager
	 */
	private function getBlockManager()
	{
		return $this->container->get('ns_cms.manager.block');
	}
}
