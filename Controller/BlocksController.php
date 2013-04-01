<?php

namespace NS\CatalogBundle\Controller;

use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use NS\CatalogBundle\Block\Settings\CategoriesBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\CategoriesMenuBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\ItemsBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\NewItemsBlockSettingsModel;
use NS\CatalogBundle\Entity\Category;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Menu\CategoryNode;
use NS\CatalogBundle\Menu\Matcher\Voter\CategoryVoter;
use NS\CmsBundle\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use NS\CmsBundle\Entity\Block;
use NS\CmsBundle\Manager\BlockManager;

use NS\CatalogBundle\Block\Settings\MainItemsBlockSettingsModel;
use NS\CatalogBundle\Entity\ItemRepository;
use Symfony\Component\Routing\RouterInterface;

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

		$slug = $this->getRequest()->attributes->get('slug');
		$category = $categoryRepository->findOneBySlug($slug);
		$matcher = new Matcher();
		if ($category) {
			$matcher->addVoter(new CategoryVoter($category));
		}

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

		$slug = $this->getRequest()->attributes->get('slug');
		$category = $categoryRepository->findOneBySlug($slug);

		$categories = $categoryRepository->findByCategory($category);

		return $this->render('NSCatalogBundle:Blocks:categoriesBlock.html.twig', array(
			'block'      => $block,
			'settings'   => $settings,
			'categories' => $categories,
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

		$slug = $this->getRequest()->attributes->get('slug');
		$category = $categoryRepository->findOneBySlug($slug);

		/** @var $itemRepository ItemRepository */
		$itemRepository = $this->getDoctrine()->getManager()->getRepository('NSCatalogBundle:Item');
		$query = $itemRepository->getFindByCategoryQuery($category);

//		$pagination = $this->get('knp_paginator')->paginate(
//			$query,
//			(!empty($_GET['page']) ? $_GET['page'] : 1),
//			$settings->getCount()
//		);

		return $this->render('NSCatalogBundle:Blocks:itemsBlock.html.twig', array(
			'block'      => $block,
			'settings'   => $settings,
			'items'      => $query->getResult(),
//			'pagination' => $pagination,
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
