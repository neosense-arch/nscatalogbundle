<?php

namespace NS\CatalogBundle;

use NS\CatalogBundle\DependencyInjection\Compiler\FormPass;
use NS\CatalogBundle\DependencyInjection\Compiler\SettingsFormTypePass;
use NS\CoreBundle\Bundle\CoreBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class NSCatalogBundle
 *
 * @package NS\CatalogBundle
 */
class NSCatalogBundle extends Bundle implements CoreBundle
{
	/**
	 * @param ContainerBuilder $container
	 */
	public function build(ContainerBuilder $container)
	{
		parent::build($container);

		$container->addCompilerPass(new SettingsFormTypePass());
		$container->addCompilerPass(new FormPass());
	}

    /**
     * Retrieves human-readable bundle title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Каталог';
    }}
