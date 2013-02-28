<?php

namespace NS\CatalogBundle;

use NS\CatalogBundle\DependencyInjection\Compiler\SettingsFormTypePass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class NSCatalogBundle extends Bundle
{
	/**
	 * @param ContainerBuilder $container
	 */
	public function build(ContainerBuilder $container)
	{
		parent::build($container);

		$container->addCompilerPass(new SettingsFormTypePass());
	}
}
