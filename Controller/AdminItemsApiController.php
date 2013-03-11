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
	 * @return Response
	 */
	public function formAction()
	{
		try {
			$itemType = new ItemType();
			$itemType->setCatalogName(self::CATALOG_NAME);

			$item = $this->getItem();
			$form = $this->createForm($itemType, $item);

			if ($this->getRequest()->getMethod() === 'POST') {
				$form->bind($this->getRequest());
				if ($form->isValid()) {
					$this->getDoctrine()->getManager()->persist($item);
					$this->getDoctrine()->getManager()->flush();
					return new JsonResponse(array('itemId' => $item->getId()));
				}
			}

			return $this->render('NSAdminBundle:Generic:form-api.html.twig', array(
				'form' => $form->createView()
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
}
