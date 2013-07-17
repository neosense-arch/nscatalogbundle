<?php

namespace NS\CatalogBundle\Controller;

use NS\CatalogBundle\Entity\CatalogRepository;
use NS\CatalogBundle\Entity\Category;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Form\Type\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AdminCategoriesApiController extends Controller
{
	const CATALOG_NAME = 'goods';

	/**
	 * @return Response
	 */
	public function formAction()
	{
		try {
			/** @var $categoryType CategoryType */
			$categoryType = $this->get('ns_catalog.form.type.category');
			$categoryType->setCatalogName(self::CATALOG_NAME);

			$category = $this->getCategory();
			$form = $this->createForm($this->get('ns_catalog.form.type.category'), $category);

			if ($this->getRequest()->getMethod() === 'POST') {
				$form->submit($this->getRequest());
				if ($form->isValid()) {
					if (!$category->getParent()) {
						$category->setParent($this->getCategoryRepository()->findRootOrCreate());
					}
					$this->getDoctrine()->getManager()->persist($category);
					$this->getDoctrine()->getManager()->flush();
					return new JsonResponse(array('categoryId' => $category->getId()));
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
			$category = $this->getCategory();
			$this->getDoctrine()->getManager()->remove($category);
			$this->getDoctrine()->getManager()->flush();
			return new JsonResponse(array('result' => 'ok'));
		}
		catch (\Exception $e) {
			return new JsonResponse(array('error' => $e->getMessage()));
		}
	}

	/**
	 * @throws \Exception
	 * @return Category
	 */
	private function getCategory()
	{
		$category = new Category();

		if (!empty($_GET['id'])) {
			$category = $this
				->getCategoryRepository()
				->findOneById($_GET['id']);
			if (!$category) {
				throw new \Exception("Category #{$_GET['id']} wasn't found");
			}
		}

		$catalog = $this->getCatalogRepository()->findOneByName(self::CATALOG_NAME);
		if (!$catalog) {
			throw new \Exception(sprintf("Catalog '{%s}' wasn't found", self::CATALOG_NAME));
		}

		$category->setCatalog($catalog);

		return $category;
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
