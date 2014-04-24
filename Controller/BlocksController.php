<?php

namespace NS\CatalogBundle\Controller;

use Doctrine\ORM\Query;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use NS\CatalogBundle\Block\Settings\CategoriesBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\CategoriesMenuBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\CategoryBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\ItemBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\ItemsBlockSettingsModel;
use NS\CatalogBundle\Block\Settings\SearchBlockSettingsModel;
use NS\CatalogBundle\Entity\Category;
use NS\CatalogBundle\Entity\CategoryRepository;
use NS\CatalogBundle\Menu\CategoryNode;
use NS\CatalogBundle\Menu\Matcher\Voter\CategoryVoter;
use NS\CatalogBundle\Service\CatalogService;
use NS\CatalogBundle\Service\ItemService;
use NS\CmsBundle\Block\Settings\Generic\CountBlockSettingsModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use NS\CmsBundle\Entity\Block;
use NS\CmsBundle\Manager\BlockManager;
use NS\CatalogBundle\Entity\ItemRepository;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class BlocksController
 *
 * @package NS\CatalogBundle\Controller
 */
class BlocksController extends Controller
{
    /**
     * Categories menu block
     *
     * @param  Block                           $block
     * @param CategoriesMenuBlockSettingsModel $settings
     * @param string|null                      $categorySlug
     * @return Response
     */
    public function categoriesMenuBlockAction(Block $block, CategoriesMenuBlockSettingsModel $settings, $categorySlug = null)
    {
        /** @var $categoryRepository CategoryRepository */
        $categoryRepository = $this->getDoctrine()->getManager()->getRepository('NSCatalogBundle:Category');

        // current category
        $currentCategory = null;
        if ($categorySlug) {
            $currentCategory = $categoryRepository->findOneBySlug($categorySlug);
        }

        // if category is set in settings
        if ($settings->getCategoryId()) {
            $currentCategory = $categoryRepository->find($settings->getCategoryId());
            $settings->setIsSubmenu(true);
        }

        // retrieving root category
        if ($settings->getIsSubmenu() && $currentCategory) {
            $rootCategory = $currentCategory;
            while ($rootCategory->getParent() && $rootCategory->getParent()->getParent()) {
                $rootCategory = $rootCategory->getParent();
            }
        } else {
            $rootCategory = $categoryRepository->findRootOrCreate();
        }

        // creating from root node
        $factory = new MenuFactory();
        /** @var $router RouterInterface */
        $router   = $this->get('router');
        $rootNode = new CategoryNode($rootCategory, $router, $settings->getRouteName());
        $menu     = $factory->createFromNode($rootNode);

        $matcher = new Matcher();
        if ($currentCategory) {
            $matcher->addVoter(new CategoryVoter($currentCategory));
        }

        // items
        $items = $menu->getChildren();

        // sorting items
        /** @var ItemInterface[] $sorted */
        $sorted    = array();
        $sortItems = explode(',', $settings->getSortOrder());
        foreach ($sortItems as $slug) {
            foreach ($items as $item) {
                /** @var Category $currentCategory */
                $category = $item->getExtra('category');
                if ($category->getSlug() === $slug) {
                    $sorted[] = $item;
                    break;
                }
            }
        }

        foreach ($items as $item) {
            /** @var Category $currentCategory */
            $category = $item->getExtra('category');
            if (!in_array($category->getSlug(), $sortItems)) {
                $sorted[] = $item;
            }
        }
        $menu->setChildren($sorted);

        // checking active items
        $hasActiveItems = false;
        if ($currentCategory) {
            foreach ($sorted as $item) {
                if ($matcher->isCurrent($item) || $matcher->isAncestor($item)) {
                    $hasActiveItems = true;
                    break;
                }
            }
        }

        // checking automatic redirect option
        if (!$hasActiveItems && $settings->getRedirectToFirstItem() && count($sorted)) {
            return $this->redirect(
                $this->generateUrl(
                    $settings->getRouteName(),
                    array(
                        'categorySlug' => $sorted[0]->getExtra('category')->getSlug(),
                    )
                )
            );
        }

        return $this->render($block->getTemplate(), array(
            'block'           => $block,
            'settings'        => $settings,
            'menu'            => $menu,
            'matcher'         => $matcher,
            'currentCategory' => $currentCategory,
        ));
    }

    /**
     * Categories block
     *
     * @param  Block                       $block
     * @param CategoriesBlockSettingsModel $settings
     * @param string|null                  $categorySlug
     * @return Response
     */
    public function categoriesBlockAction(Block $block, CategoriesBlockSettingsModel $settings, $categorySlug = null)
    {
        /** @var $categoryRepository CategoryRepository */
        $categoryRepository = $this->getDoctrine()->getManager()->getRepository('NSCatalogBundle:Category');

        if ($settings->getCategoryId()) {
            $category = $categoryRepository->find($settings->getCategoryId());
        }
        else {
            $category   = $categoryRepository->findOneBySlug($categorySlug);
        }
        $categories = $categoryRepository->findByCategory($category);

        // sorting items
        $sorted    = array();
        $sortItems = explode(',', $settings->getSortOrder());
        foreach ($sortItems as $slug) {
            foreach ($categories as $category) {
                if ($category->getSlug() === $slug) {
                    $sorted[] = $category;
                    break;
                }
            }
        }
        foreach ($categories as $category) {
            if (!in_array($category->getSlug(), $sortItems)) {
                $sorted[] = $category;
            }
        }
        $categories = $sorted;

        return $this->render($block->getTemplate(), array(
            'block'      => $block,
            'settings'   => $settings,
            'categories' => $categories,
        ));
    }

    /**
     * Category block
     *
     * @param  Block                     $block
     * @param CategoryBlockSettingsModel $settings
     * @param string                     $categorySlug
     * @return Response
     */
    public function categoryBlockAction(Block $block, CategoryBlockSettingsModel $settings, $categorySlug = null)
    {
        /** @var $categoryRepository CategoryRepository */
        $categoryRepository = $this->getDoctrine()->getManager()->getRepository('NSCatalogBundle:Category');

        $category = $categoryRepository->findOneBySlug($categorySlug);
        if (!$category) {
            return Response::create('', 404);
        }

        return $this->render($block->getTemplate(), array(
            'block'          => $block,
            'settings'       => $settings,
            'category'       => $category,
            'rootCategories' => $categoryRepository->findRootOrCreate()->getChildren(),
        ));
    }

    /**
     * Category items block
     *
     * @param Request                 $request
     * @param Block                   $block
     * @param ItemsBlockSettingsModel $settings
     * @param string                  $categorySlug
     * @return Response
     */
    public function itemsBlockAction(Request $request, Block $block, ItemsBlockSettingsModel $settings, $categorySlug = null)
    {
        /** @var CatalogService $catalogService */
        $catalogService = $this->get('ns_catalog_service');

        // filtering by category
        $filterCategory = null;
        if ($settings->getUseCategory()) {
            $filterCategory = $catalogService->getCategoryBySlug($categorySlug);
        } else if ($settings->getCategoryId()) {
            $filterCategory = $catalogService->getCategory($settings->getCategoryId());
        }

        // retrieving items
        $items = $catalogService->getItemsPaged(
            $request->query->get('page', 1),
            $settings->getCount(),
            true,
            $filterCategory,
            $settings->getSettingsConditions(),
            $settings->getOrderArray(),
            null,
            $settings->getRecursive()
        );

        return $this->render($block->getTemplate('NSCatalogBundle:Blocks:itemsBlock.html.twig'), array(
            'block'      => $block,
            'settings'   => $settings,
            'items'      => $items,
            'pagination' => $items,
            'category'   => $filterCategory,
        ));
    }

    /**
     * Item detail info block
     *
     * @param Block                  $block
     * @param ItemBlockSettingsModel $settings
     * @param string                 $itemSlug
     * @param string                 $categorySlug
     * @return Response
     */
    public function itemBlockAction(Block $block, ItemBlockSettingsModel $settings, $itemSlug = null, $categorySlug = null)
    {
        /** @var CatalogService $catalogService */
        $catalogService = $this->get('ns_catalog_service');
        $item = $catalogService->getItemBySlug($itemSlug);

        // parent category check
        if ($categorySlug) {
            $category = $catalogService->getCategoryBySlug($categorySlug);
            if (!$category || $item->getCategory() !== $category) {
                $item = null;
            }
        }

        if (!$item) {
            return Response::create('', 404);
        }

        return $this->render($block->getTemplate(), array(
            'block'    => $block,
            'settings' => $settings,
            'item'     => $item,
        ));
    }

    /**
     * @param  Block $block
     * @return Response
     */
    public function fullListBlockAction(Block $block)
    {
        /** @var $settings CountBlockSettingsModel */
        $settings = $this
            ->getBlockManager()
            ->getBlockSettings($block);

        $pagination = $this->createPagination(
            $this->getItemRepository()->getFindFullCatalogQuery(),
            $settings->getCount()
        );

        return $this->render('NSCatalogBundle:Blocks:fullListBlock.html.twig', array(
            'block'      => $block,
            'settings'   => $settings,
            'items'      => $pagination,
            'pagination' => $pagination,
        ));
    }

    /**
     * @param Request                  $request
     * @param  Block                   $block
     * @param SearchBlockSettingsModel $settings
     * @return Response
     */
    public function searchBlockAction(Request $request, Block $block, SearchBlockSettingsModel $settings)
    {
        // search query
        $query = $request->query->get('query');

        /** @var CatalogService $catalogService */
        $catalogService = $this->get('ns_catalog_service');

        // category
        $category = null;
        $categoryId = $request->query->get('category', $settings->getCategoryId());
        if ($categoryId) {
            $category = $catalogService->getCategory($categoryId);
        }

        // searching items
        $items = $catalogService->getItemsPaged(
            1, 30, true, $category, array(),
            array(array('price', 'ASC', 'number')),
            $query
        );

        return $this->render($block->getTemplate(), array(
            'block'    => $block,
            'settings' => $settings,
            'items'    => $items,
            'query'    => $query,
        ));
    }

    /**
     * @return BlockManager
     */
    private function getBlockManager()
    {
        return $this->get('ns_cms.manager.block');
    }

    /**
     * @return ItemRepository
     */
    private function getItemRepository()
    {
        return $this->get('ns_catalog.repository.item');
    }

    /**
     * @return ItemService
     */
    private function getItemService()
    {
        return $this->get('ns_catalog.service.item');
    }

    /**
     * @param Query $query
     * @param int   $itemsPerPage
     * @return PaginationInterface
     */
    private function createPagination(Query $query, $itemsPerPage)
    {
        return $this->get('knp_paginator')->paginate(
            $query,
            $this->getRequest()->query->get('page', 1),
            $itemsPerPage
        );
    }
}
