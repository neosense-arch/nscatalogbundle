<?php

namespace NS\CatalogBundle\Controller;

use NS\CatalogBundle\Entity\Catalog;
use NS\CatalogBundle\Entity\CatalogRepository;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Entity\ItemRepository;
use NS\CatalogBundle\Form\Type\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
		/** @var $form Form */
		$form = $this->get($catalog->getFormTypeName());

		$items = $this->getItemRepository()->findAll();

		return $this->render('NSCatalogBundle:AdminCatalog:index.html.twig', array(
			'items'       => $items,
			'catalog'     => $catalog,
			'catalogForm' => $form,
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
	 * @return ItemRepository
	 */
	private function getItemRepository()
	{
		return $this->getDoctrine()
			->getManager()
			->getRepository('NSCatalogBundle:Item');
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
}
