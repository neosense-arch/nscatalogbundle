<?php

namespace NS\CatalogBundle\Controller;

use NS\CatalogBundle\Entity\Catalog;
use NS\CatalogBundle\Entity\CatalogRepository;
use NS\CatalogBundle\Entity\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminCategoriesController
 *
 * @package NS\CatalogBundle\Controller
 */
class AdminCategoriesController extends Controller
{
	/**
	 * Category tree block
	 *
	 * @throws \Exception
	 * @return Response
	 */
	public function categoryTreeAction()
	{
		// catalog object
		$catalog = $this->getCatalog();

		// categories for dynatree
		$categories = $this
			->getCategoryRepository()
			->findForDynatree($catalog);

		// retrieving selected category
		$category = null;
		if (!empty($_GET['categoryId'])) {
			$category = $this
				->getCategoryRepository()
				->findOneById($_GET['categoryId']);

			if (!$category) {
				throw new \Exception("Category #{$_GET['categoryId']} wasn't found");
			}
		}

		return $this->render('NSCatalogBundle:AdminCategories:category-tree.html.twig', array(
			'categoriesJson' => json_encode($categories),
			'category'       => $category,
			'catalog'        => $catalog,
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
	 * @return CategoryRepository
	 */
	private function getCategoryRepository()
	{
		return $this->getDoctrine()
			->getManager()
			->getRepository('NSCatalogBundle:Category');
	}

	/**
	 * @return CatalogRepository
	 */
	private function getCatalogRepository()
	{
		return $this->getDoctrine()->getRepository('NSCatalogBundle:Catalog');
	}
}
