<?php

namespace NS\CatalogBundle\Controller;

use NS\CatalogBundle\Entity\Catalog;
use NS\CatalogBundle\Entity\CatalogRepository;
use NS\CatalogBundle\Entity\Category;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Entity\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
			->getFindByCategoryQuery($category, $search);

		$pagination = $this->get('knp_paginator')->paginate(
			$query,
			(!empty($_GET['page']) ? $_GET['page'] : 1),
			50
		);

		return $this->render('NSCatalogBundle:AdminCatalog:index.html.twig', array(
			'pagination'  => $pagination,
			'catalog'     => $catalog,
			'catalogForm' => $form,
			'category'    => $category,
			'search'      => $search,
		));
	}

	/**
	 * @return Catalog
	 * @throws \Exception
	 */
	private function getCatalog()
	{
		$catalog = $this->getCatalogRepository()->findOneByName('goods');
		if (!$catalog) {
			throw new \Exception("Catalog named 'goods' wasn't found");
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
