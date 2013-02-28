<?php

namespace NS\CatalogBundle\Controller;

use NS\CatalogBundle\Entity\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminCategoriesController extends Controller
{
	/**
	 * @return Response
	 */
	public function indexAction()
	{
		return $this->render('NSCatalogBundle:AdminCategories:index.html.twig', array(
			'categories' => $this->getCategoryRepository()->findAll(),
		));
	}

	/**
	 * Category tree block
	 *
	 * @return Response
	 */
	public function categoryTreeAction()
	{
		$categories = $this->getCategoryRepository()->findPagesForDynatree();

		$categoryId = null;
		if (!empty($_GET['categoryId'])) {
			$categoryId = $_GET['categoryId'];
		}

		return $this->render('NSCatalogBundle:AdminCategories:block-category-tree.html.twig', array(
			'categoriesJson' => json_encode($categories),
			'categoryId'     => $categoryId,
		));
	}

	/**
	 * @return Response
	 */
	public function formAction()
	{
		// edit mode
		if (!empty($_GET['id'])) {
			$type = $this
				->getCategoryRepository()
				->findOneById($_GET['id']);

			if (!$type) {
				return $this->back();
			}
		}

		// creation mode
		else {
			$type = new Type();
		}

		// initializing form
		$form = $this->createForm(new TypeType(), $type);

		// validating form
		if ($this->getRequest()->getMethod() === 'POST') {
			$form->bind($this->getRequest());
			if ($form->isValid()) {
				$this->getDoctrine()->getManager()->persist($type);
				$this->getDoctrine()->getManager()->flush();
				return $this->back();
			}
		}

		return $this->render('NSAdminBundle:Generic:form-with-left-panel.html.twig', array(
			'form'       => $form->createView(),
			'form_label' => $type->getId() ? 'Редактирование типа данных' : 'Создание типа данных',
		));
	}

	/**
	 * @return Response
	 */
	public function deleteAction()
	{
		// edit mode
		if (!empty($_GET['id'])) {
			$category = $this
				->getCategoryRepository()
				->findOneById($_GET['id']);

			if (!$category) {
				return $this->back();
			}

			$this->getDoctrine()->getManager()->remove($category);
			$this->getDoctrine()->getManager()->flush();
		}

		return $this->back();
	}

	/**
	 * @return RedirectResponse
	 */
	private function back()
	{
		return $this->redirect($this->generateUrl(
			'ns_admin_bundle', array(
				'adminBundle'     => 'NSCatalogBundle',
				'adminController' => 'type',
				'adminAction'     => 'index',
			)
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
