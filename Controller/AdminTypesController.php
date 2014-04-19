<?php

namespace NS\CatalogBundle\Controller;

use NS\CatalogBundle\Entity\Type;
use NS\CatalogBundle\Entity\TypeRepository;
use NS\CatalogBundle\Form\Type\TypeElementOptionsNodeSelectType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminTypesController
 *
 * @package NS\CatalogBundle\Controller
 */
class AdminTypesController extends Controller
{
	/**
	 * @return Response
	 */
	public function indexAction()
	{
        /** @var TypeRepository $typeRepository */
        $typeRepository = $this->get('ns_catalog.repository.type');
		return $this->render('NSCatalogBundle:AdminTypes:index.html.twig', array(
			'types' => $typeRepository->findAll(),
		));
	}

    /**
     * @param Request $request
     * @return Response
     */
	public function formAction(Request $request)
	{
        /** @var TypeRepository $typeRepository */
        $typeRepository = $this->get('ns_catalog.repository.type');

        $id = $request->query->get('id');
		if ($id) {
			$type = $typeRepository->find($id);
			if (!$type) {
				return $this->back();
			}
		}
		else {
			$type = new Type();
		}

        // removing elements
        $em = $this->getDoctrine()->getManager();
        foreach ($type->getElements() as $element) {
            $em->remove($element);
        }

		$form = $this->createForm('ns_catalog_type', $type);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($type);
            $this->getDoctrine()->getManager()->flush();
            return $this->back();
        }

		return $this->render('NSCatalogBundle:AdminTypes:form.html.twig', array(
			'form' => $form->createView(),
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
				->find($_GET['id']);

			if (!$category) {
				return $this->back();
			}

			$this->getDoctrine()->getManager()->remove($category);
			$this->getDoctrine()->getManager()->flush();
		}

		return $this->back();
	}

    /**
     * @param Request $request
     * @return Response
     */
    public function typeElementOptionsDialogAction(Request $request)
    {
        $typeElementCategory = $request->query->get('typeElementCategory');

        $formType = null;
        switch ($typeElementCategory) {
            case 'ns_catalog_node_select':
                $formType = new TypeElementOptionsNodeSelectType();
                break;
        }

        $form = null;
        if ($formType) {
            $form = $this->createForm($formType);
        }

        return $this->render('NSCatalogBundle:AdminTypes:typeElementOptionsDialog.html.twig', array(
            'typeElementCategory' => $typeElementCategory,
            'form'                => $form ? $form->createView() : null,
        ));
    }

	/**
	 * @return RedirectResponse
	 */
	private function back()
	{
		return $this->redirect($this->generateUrl(
			'ns_admin_bundle', array(
				'adminBundle'     => 'NSCatalogBundle',
				'adminController' => 'types',
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
