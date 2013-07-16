<?php

namespace NS\CatalogBundle\Menu\Resolver;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use NS\AdminBundle\Menu\Resolver\MenuResolverInterface;
use NS\AdminBundle\Service\AdminService;
use NS\CatalogBundle\Entity\Catalog;
use NS\CatalogBundle\Entity\CatalogRepository;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class CatalogMenuResolver
 *
 * @package NS\CatalogBundle\Menu\Resolver
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
		/** @var Catalog $catalog */
		foreach ($this->catalogRepository->findAll() as $catalog) {
			$uri = $this->router->generate('ns_admin_bundle', array(
				'adminBundle'     => 'NSCatalogBundle',
				'adminController' => 'catalog',
				'adminAction'     => 'index',
			));
			$uri .= '?catalog=' . $catalog->getName();

			$menu->addChild($this->factory->createItem(uniqid(), array(
				'label' => $catalog->getTitle(),
				'uri' => $uri,
				'displayChildren' => false
			)));
		}
	}
}
