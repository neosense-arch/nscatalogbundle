<?php

namespace NS\CatalogBundle\Controller;

use NS\CatalogBundle\Entity\Item;
use NS\CatalogBundle\Form\Type\ItemType;
use NS\CatalogBundle\Service\CatalogService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
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
     * Creates or updates item object
     *
     * @param Request $request
     * @return Response
     */
	public function formAction(Request $request)
	{
		try {
            // retrieving item
			$item = $this->getRequestItem($request, false);

            // initializing item forms
			$itemForm = $this->createItemForm($item);
			$itemSettingsForm = $this->createItemSettingsForm($item);

            // handling forms
            $itemForm->handleRequest($request);
            $itemSettingsForm->handleRequest($request);
            if ($itemForm->isValid() && $itemSettingsForm->isValid()) {
                // updating settings
                $item->setSettings($itemSettingsForm->getData());

                /** @var CatalogService $catalogService */
                $catalogService = $this->get('ns_catalog_service');
                $catalogService->updateItem($item);

                return new JsonResponse(array(
                    'itemId' => $item->getId(),
                ));
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
     * @param Request $request
     * @return Response
     */
	public function updateCustomSettingAction(Request $request)
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

			$item = $this->getRequestItem($request);

			$settings = $item->getSettings();
			$settings->setSetting($_REQUEST['field'], $_REQUEST['value']);
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
     * @param Request $request
     * @return Response
     */
	public function updateBasePropertyAction(Request $request)
	{
		try {
            $value = $request->request->get('value');
			if (is_null($value)) {
				return new JsonResponse(array('error' => "Required param 'value' wasn't found"));
			}

            $field = $request->request->get('field');
			if (is_null($field)) {
				return new JsonResponse(array('error' => "Required param 'field' wasn't found"));
			}

			$item = $this->getRequestItem($request);

			$method = 'set' . ucfirst($field);
			$item->$method(trim($value));

			$this->getDoctrine()->getManager()->persist($item);
			$this->getDoctrine()->getManager()->flush();
			return new JsonResponse(array('result' => 'ok'));
		}
		catch (\Exception $e) {
			return new JsonResponse(array('error' => $e->getMessage()));
		}
	}

    /**
     * Removes item
     *
     * @param Request $request
     * @return Response
     */
	public function deleteAction(Request $request)
	{
		try {
			$item = $this->getRequestItem($request);

            /** @var CatalogService $catalogService */
            $catalogService = $this->get('ns_catalog_service');
			$catalogService->removeItem($item);

			return new JsonResponse(array('result' => 'ok'));
		}
		catch (\Exception $e) {
			return new JsonResponse(array('error' => $e->getMessage()));
		}
	}

    /**
     * Moves selected items to selected category
     *
     * @param Request $request
     * @return Response
     */
	public function updateCategoryAction(Request $request)
	{
		try {
            // retrieving category id
            $categoryId = $request->query->get('categoryId');
			if (!$categoryId) {
				return new JsonResponse(array('error' => "Required param 'categoryId' wasn't found"));
			}

            /** @var CatalogService $catalogService */
            $catalogService = $this->get('ns_catalog_service');

            // retrieving category
			$category = $catalogService->getCategory($categoryId);
			if (!$category) {
				return new JsonResponse(array('error' => "Category #{$categoryId} wasn't found"));
			}

            $items = $catalogService->getItemsByIds($this->getRequestIds($request));
            $catalogService->setItemsCategory($items, $category);

			return new JsonResponse(array('result' => 'ok'));
		}
		catch (\Exception $e) {
			return new JsonResponse(array('error' => $e->getMessage()));
		}
	}

    /**
     * @param Request $request
     * @return Response
     */
	public function cloneItemsAction(Request $request)
	{
		try {
            /** @var CatalogService $catalogService */
            $catalogService = $this->get('ns_catalog_service');

            $items = $catalogService->getItemsByIds($this->getRequestIds($request));
            $catalogService->cloneItems($items);

			return new JsonResponse(array('result' => 'ok'));
		}
		catch (\Exception $e) {
			return new JsonResponse(array('error' => $e->getMessage()));
		}
	}

    /**
     * Reorders pages
     *
     * @return JsonResponse
     */
    public function ajaxReorderAction()
    {
        try {
            // checking page id
            if (empty($_GET['id'])) {
                return new JsonResponse(array('error' => 'Param "id" is required'));
            }

            /** @var CatalogService $catalogService */
            $catalogService = $this->get('ns_catalog_service');
            $item = $catalogService->getItem($_GET['id']);
            if (!$item) {
                return new JsonResponse(array('error' => "Item #{$_GET['id']} wasn't found"));
            }

            // checking position
            if (!isset($_GET['position'])) {
                return new JsonResponse(array('error' => 'Param "position" is required'));
            }

            // reordering
            $item->setPosition($_GET['position']);
            $catalogService->updateItem($item);

            // retrieving new position
            return new JsonResponse(array('position' => $item->getPosition()));
        }
        catch (\Exception $e) {
            return new JsonResponse(array('error' => "Exception occurred: {$e->getMessage()}"));
        }
    }

    /**
     * @param Request $request
     * @throws \Exception
     * @return array|JsonResponse
     */
    private function getRequestIds(Request $request)
    {
        $id = $request->request->get('id');
        if (!$id) {
            throw new \Exception("Required param 'id' wasn't found");
        }
        return explode(',', $id);
    }

    /**
     * @param Request $request
     * @param bool    $strict
     * @throws \Exception
     * @return Item
     */
	private function getRequestItem(Request $request, $strict = true)
	{
		$item = new Item();

        $itemId = $request->request->get('id', $request->query->get('id'));
        if (!$itemId && $strict) {
            throw new \Exception("Required param 'id' wasn't found");
        }

        /** @var CatalogService $catalogService */
        $catalogService = $this->get('ns_catalog_service');

		if ($itemId) {
            $item = $catalogService->getItem($itemId);
			if (!$item) {
				throw new \Exception("Item #{$itemId} wasn't found");
			}
		}

        $categoryId = null;
        if (!$categoryId) {
            $categoryId = $request->request->get('categoryId');
        }
        if (!$categoryId) {
            $categoryId = $request->query->get('categoryId');
        }
        if (!$categoryId) {
            $form = $request->request->get('ns_catalogbundle_itemtype');
            $categoryId = $form['category'];
        }
		if ($categoryId) {
			$category = $catalogService->getCategory($categoryId);
			if (!$category) {
				throw new \Exception("Category #{$categoryId} wasn't found");
			}
			$item->setCategory($category);
		}

		return $item;
	}

	/**
	 * @param Item $item
	 * @return Form
	 */
	private function createItemForm(Item $item)
	{
		$itemType = new ItemType();
		$itemType->setCatalogName(self::CATALOG_NAME);

		return $this->createForm($itemType, $item);
	}

	/**
	 * @param Item $item
	 * @throws \Exception
	 * @return Form
	 */
	private function createItemSettingsForm(Item $item)
	{
        /** @var CatalogService $catalogService */
        $catalogService = $this->get('ns_catalog_service');
        $catalog = $catalogService->getCatalogByName(self::CATALOG_NAME);
		if (!$catalog) {
			throw new \Exception(sprintf("Catalog '%s' wasn't found", self::CATALOG_NAME));
		}

        $category = $item->getCategory();
        if ($category && $category->getType()) {
            return $this->createForm('ns_catalog_node', $item->getSettings() ?: null, array(
                'type' => $category->getType(),
            ));
        }

		$itemSettingsType = $this->get($catalog->getSettingsFormTypeName());
		return $this->createForm($itemSettingsType, $item->getSettings() ?: null);
	}
}
