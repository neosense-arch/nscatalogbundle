<?php

namespace NS\CatalogBundle\Event;

use NS\CatalogBundle\Service\CatalogService;
use NS\CmsBundle\Entity\Page;
use NS\CmsBundle\Service\PageService;

/**
 * Class InstallListener
 *
 * @package NS\CatalogBundle\Event
 */
class InstallListener
{
    /**
     * @var PageService
     */
    private $pageService;

    /**
     * @var CatalogService
     */
    private $catalogService;

    /**
     * @param PageService    $pageService
     * @param CatalogService $catalogService
     */
    public function __construct(PageService $pageService, CatalogService $catalogService)
    {
        $this->pageService    = $pageService;
        $this->catalogService = $catalogService;
    }

    /**
     * Install event
     *
     * @return mixed
     */
    public function onInstall()
    {
        // creating pages
        $catalogPage = $this->pageService->createPage('Каталог', 'catalog', Page::MAIN_PAGE_NAME);
        $this->pageService->updatePage($catalogPage);

        $itemPage = $this->pageService->createPage('Страница товара', 'item', $catalogPage);
        $this->pageService->updatePage($itemPage);

        $categoryPage = $this->pageService->createPage('Страница категории', 'category', $catalogPage);
        $this->pageService->updatePage($categoryPage);

        // creating 'goods' catalog
        $goods = $this->catalogService->createCatalog('Контент', 'goods');
        $this->catalogService->updateCatalog($goods);
    }
}