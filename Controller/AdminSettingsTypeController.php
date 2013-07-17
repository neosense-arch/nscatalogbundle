<?php

namespace NS\CatalogBundle\Controller;

use NS\CatalogBundle\Form\Type\TypeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use NS\CatalogBundle\Entity\Type;
use NS\CatalogBundle\Entity\TypeRepository;

class AdminSettingsTypeController extends Controller
{
	/**
	 * @return Response
	 */
	public function indexAction()
	{
		return $this->render('NSCatalogBundle:AdminSettingsType:index.html.twig', array(
			'types' => $this->getTypeRepository()->findAll(),
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
				->getTypeRepository()
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
			$form->submit($this->getRequest());
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
				->getTypeRepository()
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
	 * @return TypeRepository
	 */
	private function getTypeRepository()
	{
		return $this->getDoctrine()
			->getManager()
			->getRepository('NSCatalogBundle:Type');
	}
}
