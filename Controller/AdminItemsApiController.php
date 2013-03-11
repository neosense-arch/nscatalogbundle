<?php

namespace NS\CatalogBundle\Controller;

use NS\CatalogBundle\Entity\CatalogRepository;
use NS\CatalogBundle\Entity\Category;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Entity\Item;
use NS\CatalogBundle\Entity\ItemRepository;
use NS\CatalogBundle\Form\Type\CategoryType;
use NS\CatalogBundle\Form\Type\ItemType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AdminItemsApiController extends Controller
{
	const CATALOG_NAME = 'goods';

	/**
	 * @throws \Exception
	 * @return Response
	 */
	public function formAction()
	{
		try {
			$item = $this->getItem();
			$itemForm = $this->createItemForm($item);

			$itemSettingsForm = $this->createItemSettingsForm($item->getSettings());

			if ($this->getRequest()->getMethod() === 'POST') {
				$itemForm->bind($this->getRequest());
				$itemSettingsForm->bind($this->getRequest());

				if ($itemForm->isValid() && $itemSettingsForm->isValid()) {
					$item->setSettings($itemSettingsForm->getData());
					$this->getDoctrine()->getManager()->persist($item);
					$this->getDoctrine()->getManager()->flush();
					return new JsonResponse(array('itemId' => $item->getId()));
				}
			}

			return $this->render('NSCatalogBundle:AdminItemsApi:form.html.twig', array(
				'itemForm' => $itemForm->createView(),
				'itemSettingsForm' => $itemSettingsForm->createView(),
			));
		}
		catch (\Exception $e) {
			return new JsonResponse(array('error' => $e->getMessage()));
		}
	}

	/**
	 * Removes page
	 *
	 * @return Response
	 */
	public function deleteAction()
	{
		try {
			$item = $this->getItem();
			$this->getDoctrine()->getManager()->remove($item);
			$this->getDoctrine()->getManager()->flush();
			return new JsonResponse(array('result' => 'ok'));
		}
		catch (\Exception $e) {
			return new JsonResponse(array('error' => $e->getMessage()));
		}
	}

	/**
	 * @throws \Exception
	 * @return Item
	 */
	private function getItem()
	{
		$item = new Item();

		if (!empty($_GET['id'])) {
			$item = $this
				->getItemRepository()
				->findOneById($_GET['id']);
			if (!$item) {
				throw new \Exception("Item #{$_GET['id']} wasn't found");
			}
		}

		if (!empty($_GET['categoryId'])) {
			$category = $this->getCategoryRepository()->findOneById($_GET['categoryId']);
			if (!$category) {
				throw new \Exception("Category #{$_GET['categoryId']} wasn't found");
			}
			$item->setCategory($category);
		}

		return $item;
	}

	/**
	 * @param \NS\CatalogBundle\Entity\Item $item
	 * @return \Symfony\Component\Form\Form
	 */
	private function createItemForm(Item $item)
	{
		$itemType = new ItemType();
		$itemType->setCatalogName(self::CATALOG_NAME);

		return $this->createForm($itemType, $item);
	}

	/**
	 * @return \Symfony\Component\Form\Form
	 * @throws \Exception
	 */
	private function createItemSettingsForm()
	{
		$catalog = $this->getCatalogRepository()->findOneByName(self::CATALOG_NAME);
		if (!$catalog) {
			throw new \Exception(sprintf("Catalog '%s' wasn't found", self::CATALOG_NAME));
		}

		$itemSettingsType = $this->get($catalog->getFormTypeName());
		return $this->createForm($itemSettingsType);
	}

	/**
	 * @return ItemRepository
	 */
	private function getItemRepository()
	{
		return $this->getDoctrine()->getRepository('NSCatalogBundle:Item');
	}

	/**
	 * @return CategoryRepository
	 */
	private function getCategoryRepository()
	{
		return $this->getDoctrine()->getRepository('NSCatalogBundle:Category');
	}

	/**
	 * @return CatalogRepository
	 */
	private function getCatalogRepository()
	{
		return $this->getDoctrine()->getRepository('NSCatalogBundle:Catalog');
	}
}
