<?php

namespace NS\CatalogBundle\Controller;

use NS\CatalogBundle\Entity\Catalog;
use NS\CatalogBundle\Entity\CatalogRepository;
use NS\CatalogBundle\Entity\Category;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Entity\ItemRepository;
use NS\CatalogBundle\Form\Type\CategorySelectType;
use NS\CatalogBundle\Form\Type\ViewportConfigType;
use NS\CatalogBundle\Service\CatalogService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminCatalogController
 *
 * @package NS\CatalogBundle\Controller
 */
class AdminCatalogController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
	public function indexAction(Request $request)
	{
		$catalog = $this->getCatalog();
		$category = $this->getCategory();

        // viewport config
        $cols = array();
        $orderCol = null;
        $orderBy = array();
        $viewportConfigForm = $this->createForm(new ViewportConfigType());
        if ($category && $category->getType()) {
            $config = $category->getType()->getAdminViewportConfig();
            $viewportConfigForm->setData($config);
            if (!empty($config['elements'])) {
                foreach (explode(',', $config['elements']) as $element) {
                    $cols[] = trim($element);
                }
            }
            if (!empty($config['orderElement'])) {
                $orderBy = explode(' ', $config['orderElement']);
                $orderCol = $orderBy[0];
            }
        }

        // sortable
        $isSortable = !$orderBy;

        // search query
		$search = $request->query->get('search');

        /** @var CatalogService $catalogService */
        $catalogService = $this->get('ns_catalog_service');
        $items = $catalogService->getItemsPaged(
            $request->get('page', 1),
            50,
            null,
            $category,
            array(),
            $orderBy ? array($orderBy) : array(),
            $search,
            false,
            $isSortable
        );

		// category choice
		$categoryForm = $this->createForm(new CategorySelectType());

		return $this->render('NSCatalogBundle:AdminCatalog:index.html.twig', array(
            'pagination'         => $items,
            'catalog'            => $catalog,
            'category'           => $category,
            'search'             => $search,
            'categoryForm'       => $categoryForm->createView(),
            'viewportConfigForm' => $viewportConfigForm->createView(),
            'cols'               => $cols,
            'orderCol'           => $orderCol,
            'isSortable'         => $isSortable,
		));
	}

	/**
	 * @return Catalog
	 * @throws \Exception
	 */
	private function getCatalog()
	{
		$catalogName = 'goods';
		if (!empty($_GET['catalog'])) {
			$catalogName = $_GET['catalog'];
		}

		$catalog = $this->getCatalogRepository()->findOneByName($catalogName);
		if (!$catalog) {
			throw new \Exception("Catalog named '{$catalogName}' wasn't found");
		}

		return $catalog;
	}

	/**
	 * @return Category
	 * @throws \Exception
	 */
	private function getCategory()
	{
		if (empty($_GET['categoryId'])) {
			return null;
		}

		$category = $this->getCategoryRepository()->findOneById($_GET['categoryId']);
		if (!$category) {
			throw new \Exception("Category #{$_GET['categoryId']} wasn't found");
		}

		return $category;
	}

	/**
	 * @return CatalogRepository
	 */
	private function getCatalogRepository()
	{
		return $this->getDoctrine()
			->getManager()
			->getRepository('NSCatalogBundle:Catalog');
	}

	/**
	 * @return CategoryRepository
	 */
	private function getCategoryRepository()
	{
		return $this->getDoctrine()
			->getManager()
			->getRepository('NSCatalogBundle:Category');
	}
}
