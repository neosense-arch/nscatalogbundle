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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminItemsApiController
 *
 * @package NS\CatalogBundle\Controller
 */
class AdminItemsApiController extends Controller
{
    /**
     * @var string Default catalog name
     */
    const CATALOG_NAME = 'goods';

    /**
     * @param Request $request
     * @return Response
     */
	public function formAction(Request $request)
	{
		try {
			$item = $this->getItem();

			$itemForm = $this->createItemForm($item);
			$itemSettingsForm = $this->createItemSettingsForm($item);

            $itemForm->handleRequest($request);
            $itemSettingsForm->handleRequest($request);

            if ($itemForm->isValid() && $itemSettingsForm->isValid()) {
                $item->setSettings($itemSettingsForm->getData());
                $this->getDoctrine()->getManager()->persist($item);
                $this->getDoctrine()->getManager()->flush();
                return new JsonResponse(array('itemId' => $item->getId()));
            }

			return $this->render('NSCatalogBundle:AdminItemsApi:form.html.twig', array(
                'itemForm'         => $itemForm->createView(),
                'itemSettingsForm' => $itemSettingsForm->createView(),
                'item'             => $item,
			));
		}
		catch (\Exception $e) {
			return new JsonResponse(array('error' => $e->getMessage()));
		}
	}

	/**
	 * @throws \Exception
	 * @return Response
	 */
	public function updateCustomSettingAction()
	{
		try {
			if (empty($_REQUEST['id'])) {
				return new JsonResponse(array('error' => "Required param 'id' wasn't found"));
			}
			if (!isset($_REQUEST['value'])) {
				return new JsonResponse(array('error' => "Required param 'value' wasn't found"));
			}
			if (empty($_REQUEST['field'])) {
				return new JsonResponse(array('error' => "Required param 'field' wasn't found"));
			}

			$item = $this->getItem();

			$method = 'set' . ucfirst($_REQUEST['field']);
			$settings = $item->getSettings();
			$settings->$method($_REQUEST['value']);
			$item->setSettings($settings);

			$this->getDoctrine()->getManager()->persist($item);
			$this->getDoctrine()->getManager()->flush();
			return new JsonResponse(array('result' => 'ok'));
		}
		catch (\Exception $e) {
			return new JsonResponse(array('error' => $e->getMessage()));
		}
	}

	/**
	 * @throws \Exception
	 * @return Response
	 */
	public function updateBasePropertyAction()
	{
		try {
			if (empty($_REQUEST['id'])) {
				return new JsonResponse(array('error' => "Required param 'id' wasn't found"));
			}
			if (!isset($_REQUEST['value'])) {
				return new JsonResponse(array('error' => "Required param 'value' wasn't found"));
			}
			if (empty($_REQUEST['field'])) {
				return new JsonResponse(array('error' => "Required param 'field' wasn't found"));
			}

			$item = $this->getItem();

			$method = 'set' . ucfirst($_REQUEST['field']);
			$item->$method(trim($_REQUEST['value']));

			$this->getDoctrine()->getManager()->persist($item);
			$this->getDoctrine()->getManager()->flush();
			return new JsonResponse(array('result' => 'ok'));
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
	 * @return Response
	 */
	public function updateCategoryAction()
	{
		try {
			if (empty($_REQUEST['id'])) {
				return new JsonResponse(array('error' => "Required param 'id' wasn't found"));
			}
			if (!isset($_REQUEST['categoryId'])) {
				return new JsonResponse(array('error' => "Required param 'categoryId' wasn't found"));
			}

			$category = $this->getCategoryRepository()->findOneById($_REQUEST['categoryId']);
			if (!$category) {
				return new JsonResponse(array('error' => "Category #{$_REQUEST['categoryId']} wasn't found"));
			}

			$ids = explode(',', $_REQUEST['id']);
			$items = $this->getItemRepository()->findByIds($ids);

			foreach ($items as $item) {
				$item->setCategory($category);
			}

			$this->getDoctrine()->getManager()->flush();
			return new JsonResponse(array('result' => 'ok'));
		}
		catch (\Exception $e) {
			return new JsonResponse(array('error' => $e->getMessage()));
		}
	}

	/**
	 * @throws \Exception
	 * @return Response
	 */
	public function cloneItemsAction()
	{
		try {
			if (empty($_REQUEST['id'])) {
				return new JsonResponse(array('error' => "Required param 'id' wasn't found"));
			}

			$em = $this->getDoctrine()->getManager();

			$ids = explode(',', $_REQUEST['id']);
			$items = $this->getItemRepository()->findByIds($ids);

			foreach ($items as $item) {
				// cloning item
				$clonedItem = clone $item;
				$clonedItem->setTitle($item->getTitle() . ' (копия)');
				$em->detach($clonedItem);
				$em->persist($clonedItem);

				// cloning settings
				foreach ($item->getRawSettings() as $setting) {
					$clonedSetting = clone $setting;
					$clonedSetting->setItem($clonedItem);
					$em->detach($clonedSetting);
					$em->persist($clonedSetting);
				}
			}

			$em->flush();
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

		if (!empty($_REQUEST['id'])) {
			$item = $this
				->getItemRepository()
				->findOneById($_REQUEST['id']);
			if (!$item) {
				throw new \Exception("Item #{$_REQUEST['id']} wasn't found");
			}
		}

		if (!empty($_REQUEST['categoryId'])) {
			$category = $this->getCategoryRepository()->findOneById($_REQUEST['categoryId']);
			if (!$category) {
				throw new \Exception("Category #{$_REQUEST['categoryId']} wasn't found");
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
	 * @param \NS\CatalogBundle\Entity\Item $item
	 * @throws \Exception
	 * @return \Symfony\Component\Form\Form
	 */
	private function createItemSettingsForm(Item $item)
	{
		$catalog = $this->getCatalogRepository()->findOneByName(self::CATALOG_NAME);
		if (!$catalog) {
			throw new \Exception(sprintf("Catalog '%s' wasn't found", self::CATALOG_NAME));
		}

		$itemSettingsType = $this->get($catalog->getSettingsFormTypeName());
		return $this->createForm($itemSettingsType, $item->getSettings() ?: null);
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
