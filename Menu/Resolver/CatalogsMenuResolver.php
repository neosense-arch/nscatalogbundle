<?php

namespace NS\CatalogBundle\Menu\Resolver;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use NS\AdminBundle\Service\AdminService;
use NS\AdminBundle\Menu\Resolver\MenuResolverInterface;
use NS\CatalogBundle\Entity\Catalog;
use NS\CatalogBundle\Entity\CatalogRepository;

/**
 * Catalogs menu resolver
 *
 */
class BundlesMenuResolver implements MenuResolverInterface
{
	/**
	 * @var FactoryInterface
	 */
	private $factory;

	/**
	 * @var CatalogRepository
	 */
	private $catalogRepository;

	/**
	 * @param FactoryInterface  $factory
	 * @param CatalogRepository $catalogRepository
	 */
	public function __construct(FactoryInterface $factory, CatalogRepository $catalogRepository)
	{
		$this->factory = $factory;
		$this->catalogRepository = $catalogRepository;
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



//		$item = $this->convertDataFormat($data, $bundleName);
//		return $this->factory->createFromArray($item);
	}

	/**
	 * Converts data format from ns_admin.navigation format to KNP-Menu
	 *
	 * @param  array  $data
	 * @param  string $bundleName
	 * @throws \Exception
	 * @return array
	 */
	private function convertDataFormat(array $data, $bundleName)
	{
		// required params
		if (empty($data['label'])) {
			throw new \Exception('Required attribute "label" is missing');
		}
		if (empty($data['action'])) {
			throw new \Exception('Required attribute "action" is missing');
		}

		// if action value is empty ("mycontroller:" or "mycontroller")
		// using default value "index"
		$action = trim($data['action'], ':');

		// exploding route params (with default action value)
		$params = explode(':', $action) + array(null, 'index');
		$adminController = $params[0];
		$adminAction = $params[1];

		// retrieving knp-menu formatted item config array
		$item = array(
			'name'  => uniqid(),
			'label' => $data['label'],
			'route' => 'ns_admin_bundle',
			'routeParameters' => array(
				'adminBundle'     => $bundleName,
				'adminController' => $adminController,
				'adminAction'     => $adminAction,
			),
			'extras' => array(
				'controller' => $this->adminService->getAdminRouteController($bundleName, $adminController, $adminAction),
			),
			'displayChildren' => false
		);

		// recursively adding child items
		if (!empty($data['pages'])) {
			foreach ($data['pages'] as $page) {
				$item['children'][] = $this->convertDataFormat($page, $bundleName);
			}
		}

		return $item;
	}
}
