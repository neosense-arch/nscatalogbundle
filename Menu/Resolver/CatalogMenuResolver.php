<?php

namespace NS\CatalogBundle\Menu\Resolver;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use NS\AdminBundle\Menu\Resolver\MenuResolverInterface;
use NS\AdminBundle\Service\AdminService;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Yaml\Yaml;

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
	 * @param AdminService     $adminService
	 * @param FactoryInterface $factory
	 */
	public function __construct(AdminService $adminService, FactoryInterface $factory)
	{
		$this->adminService = $adminService;
		$this->factory = $factory;
	}

	/**
	 * @param ItemInterface $menu
	 * @return void
	 */
	public function resolve(ItemInterface $menu)
	{
		return;

		// adding bundles' menus
		foreach ($this->adminService->getActiveBundles() as $bundle) {
			$fileName = $this->getBundleNavigationFileName($bundle);
			if (file_exists($fileName)) {
				$yml = file_get_contents($fileName);
				foreach (Yaml::parse($yml) as $data) {
					$menu->addChild($this->createMenuItem($data, $bundle->getName()));
				}
			}
		}
	}

	/**
	 * @param array  $data
	 * @param string $bundleName
	 * @return ItemInterface
	 */
	private function createMenuItem(array $data, $bundleName)
	{
		$item = $this->convertDataFormat($data, $bundleName);
		return $this->factory->createFromArray($item);
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
