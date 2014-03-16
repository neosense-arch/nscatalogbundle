<?php

namespace NS\CatalogBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FormPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $templates  = array(
            'NSCatalogBundle:Form:Fields/typeElements.html.twig',
            'NSCatalogBundle:Form:Fields/typeElements.html.twig',
        );

        $resources = $container->getParameter('twig.form.resources');

        foreach ($templates as $template) {
            if (!in_array($template, $resources)) {
                array_unshift($resources, $template);
            }
        }

        $container->setParameter('twig.form.resources', $resources);
    }
}
