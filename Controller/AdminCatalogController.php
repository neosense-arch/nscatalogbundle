<?php

namespace NS\CatalogBundle\Controller;

use NS\CatalogBundle\Entity\Catalog;
use NS\CatalogBundle\Entity\CatalogRepository;
use NS\CatalogBundle\Entity\Category;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Entity\ItemRepository;
use NS\CatalogBundle\Form\Type\CategorySelectType;
use NS\CatalogBundle\QueryBuilder\ItemQueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminCatalogController
 *
 * @package NS\CatalogBundle\Controller
 */
class AdminCatalogController extends Controller
{
	/**
	 * @throws \Exception
	 * @return Response
	 */
	public function indexAction()
	{
		// catalog object
		$catalog = $this->getCatalog();

		// catalog form
		$form = $this->get($catalog->getSettingsFormTypeName());

		$category = $this->getCategory();

		$search = !empty($_GET['search']) ? $_GET['search'] : null;

		// items with pagination
		$query = $this
			->getItemRepository()
			->getFindByCategoryQuery($category, $search, $catalog);

		$pagination = $this->get('knp_paginator')->paginate(
			$query,
			(!empty($_GET['page']) ? $_GET['page'] : 1),
			50
		);

		// category choice
		$categoryForm = $this->createForm(new CategorySelectType());

		return $this->render('NSCatalogBundle:AdminCatalog:index.html.twig', array(
			'pagination'   => $pagination,
			'catalog'      => $catalog,
			'catalogForm'  => $form,
			'category'     => $category,
			'search'       => $search,
			'categoryForm' => $categoryForm->createView(),
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

	/**
	 * @return ItemRepository
	 */
	private function getItemRepository()
	{
		return $this->getDoctrine()
			->getManager()
			->getRepository('NSCatalogBundle:Item');
	}
}
