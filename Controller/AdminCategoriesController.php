<?php

namespace NS\CatalogBundle\Controller;

use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Form\Type\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
		$categories = $this->getCategoryRepository()->findForDynatree();

		$category = null;
		if (!empty($_GET['categoryId'])) {
			$category = $this->getCategoryRepository()->findOneById($_GET['categoryId']);
			if (!$category) {
				throw new \Exception("Category #{$_GET['categoryId']} wasn't found");
			}
		}

		return $this->render('NSCatalogBundle:AdminCategories:category-tree.html.twig', array(
			'categoriesJson' => json_encode($categories),
			'category'       => $category,
		));
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
