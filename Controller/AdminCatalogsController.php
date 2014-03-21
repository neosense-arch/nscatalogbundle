<?php

namespace NS\CatalogBundle\Controller;

use NS\CatalogBundle\Form\Type\CatalogType;
use NS\CatalogBundle\Service\CatalogService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminCatalogsController
 *
 * @package NS\CatalogBundle\Controller
 */
class AdminCatalogsController extends Controller
{
	/**
	 * @return Response
	 */
	public function indexAction()
	{
        /** @var CatalogService $catalogService */
        $catalogService = $this->get('ns_catalog_service');

        $catalogs = $catalogService->getCatalogs();

		return $this->render('NSCatalogBundle:AdminCatalogs:index.html.twig', array(
			'catalogs' => $catalogs,
		));
	}

    /**
     * @param Request $request
     * @return Response
     */
	public function formAction(Request $request)
	{
        /** @var CatalogService $catalogService */
        $catalogService = $this->get('ns_catalog_service');
        $catalog = $catalogService->getCatalogOrCreate($request->query->get('id'));

		$form = $this->createForm(new CatalogType(), $catalog);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $catalogService->updateCatalog($catalog);
            return $this->back();
        }

		return $this->render('NSAdminBundle:Generic:form.html.twig', array(
			'form' => $form->createView(),
		));
	}

    /**
     * @param Request $request
     * @return Response
     */
	public function deleteAction(Request $request)
	{
        /** @var CatalogService $catalogService */
        $catalogService = $this->get('ns_catalog_service');
        $catalog = $catalogService->getCatalogOrCreate($request->query->get('id'));

        $catalogService->removeCatalog($catalog);

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
				'adminController' => 'catalogs',
				'adminAction'     => 'index',
			)
		));
	}
}
