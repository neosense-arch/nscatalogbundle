<?php

namespace NS\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Knp\Menu\ItemInterface;

class AdminSettingsIndexController extends Controller
{
	/**
	 * @throws \Exception
	 * @return Response
	 */
	public function indexAction()
	{
		/** @var ItemInterface $menu  */
		$menu = $this->get('ns_admin.menu.main');

		if (!$menu->offsetExists('settings')) {
			throw new \Exception("Menu item 'settings' wasn't found");
		}

		$settings = $menu->offsetGet('settings');
		if (!$settings->hasChildren()) {
			throw new \Exception("It seems like settings is empty");
		}

		/** @var $firstChild ItemInterface */
		$firstChild = $this->getCatalogSettingsMenu($settings)->getFirstChild();
		return $this->redirect($firstChild->getUri());
	}

	/**
	 * @param  ItemInterface $menu
	 * @return ItemInterface
	 * @throws \Exception
	 */
	private function getCatalogSettingsMenu(ItemInterface $menu)
	{
		/** @var $subMenu ItemInterface */
		foreach ($menu->getChildren() as $subMenu) {
			if ($subMenu->getName() === 'settings-NSCatalogBundle') {
				return $subMenu;
			}
		}

		throw new \Exception("It seems like catalog settings section is empty");
	}
}
