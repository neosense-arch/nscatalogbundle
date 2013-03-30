<?php

namespace NS\CatalogBundle\Controller;

use NS\CatalogBundle\Block\Settings\NewItemsBlockSettingsModel;
use NS\CmsBundle\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use NS\CmsBundle\Entity\Block;
use NS\CmsBundle\Manager\BlockManager;

use NS\CatalogBundle\Block\Settings\MainItemsBlockSettingsModel;
use NS\CatalogBundle\Entity\ItemRepository;

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
	 * @return BlockManager
	 */
	private function getBlockManager()
	{
		return $this->container->get('ns_cms.manager.block');
	}
}
