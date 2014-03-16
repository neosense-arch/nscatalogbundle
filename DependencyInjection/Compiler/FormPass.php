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
        $template  = 'NSCatalogBundle:Form:fields.html.twig';
        $resources = $container->getParameter('twig.form.resources');
        // Ensure it wasnt already aded via config
        if (!in_array($template, $resources)) {
            array_unshift($resources, $template);
            $container->setParameter('twig.form.resources', $resources);
        }
    }
}
