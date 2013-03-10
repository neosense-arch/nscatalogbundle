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
	 * @return Response
	 */
	public function categoryTreeAction()
	{
		$categories = $this->getCategoryRepository()->findForDynatree();

		$categoryId = null;
		if (!empty($_GET['categoryId'])) {
			$categoryId = $_GET['categoryId'];
		}

		return $this->render('NSCatalogBundle:AdminCategories:category-tree.html.twig', array(
			'categoriesJson' => json_encode($categories),
			'categoryId'     => $categoryId,
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
