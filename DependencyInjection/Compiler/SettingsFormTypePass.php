<?php

namespace NS\CatalogBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SettingsFormTypePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
		if (!$container->hasDefinition('ns_catalog.settings.formtype.collection')) {
			return;
		}

		$definition = $container->getDefinition('ns_catalog.settings.formtype.collection');

		$formTypes = $container->findTaggedServiceIds('ns_catalog.settings.formtype');
		foreach ($formTypes as $id => $attributes) {
			$definition->addMethodCall('add', array(new Reference($id)));
		}
    }
}
