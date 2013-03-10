<?php

namespace NS\CatalogBundle\Menu\Resolver;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use NS\AdminBundle\Service\AdminService;
use NS\AdminBundle\Menu\Resolver\MenuResolverInterface;
use NS\CatalogBundle\Entity\Catalog;
use NS\CatalogBundle\Entity\CatalogRepository;
use Symfony\Component\Routing\RouterInterface;

/**
 * Catalogs menu resolver
 *
 */
class CatalogMenuResolver implements MenuResolverInterface
{
	/**
	 * @var AdminService
	 */
	private $adminService;

	/**
	 * @var FactoryInterface
	 */
	private $factory;

	/**
	 * @var CatalogRepository
	 */
	private $catalogRepository;

	/**
	 * @var RouterInterface
	 */
	private $router;

	/**
	 * @param AdminService      $adminService
	 * @param FactoryInterface  $factory
	 * @param CatalogRepository $catalogRepository
	 * @param RouterInterface   $router
	 */
	public function __construct(AdminService $adminService, FactoryInterface $factory, CatalogRepository $catalogRepository, RouterInterface $router)
	{
		$this->adminService = $adminService;
		$this->factory = $factory;
		$this->catalogRepository = $catalogRepository;
		$this->router = $router;
	}

	/**
	 * @param ItemInterface $menu
	 * @return void
	 */
	public function resolve(ItemInterface $menu)
	{
		/** @var $catalogs Catalog[] */
		$catalogs = $this->catalogRepository->findAll();

		foreach ($catalogs as $catalog) {
			$menu->addChild($this->createMenuItem($catalog));
		}
	}

	/**
	 * @param Catalog $catalog
	 * @return ItemInterface
	 */
	private function createMenuItem(Catalog $catalog)
	{
		$adminBundle = 'NSCatalogBundle';
		$adminController = 'catalog';
		$adminAction = 'index';

		$uri = $this->router->generate('ns_admin_bundle', array(
			'adminBundle'     => $adminBundle,
			'adminController' => $adminController,
			'adminAction'     => $adminAction,
		));

		$item = array(
			'name'  => uniqid(),
			'label' => $catalog->getTitle(),
			'uri' => $uri . '?catalogId=' . $catalog->getId(),
			'extras' => array(
				'controller' => $this->adminService->getAdminRouteController($adminBundle, $adminController, $adminAction),
			),
			'displayChildren' => false
		);

		return $this->factory->createFromArray($item);
	}
}
