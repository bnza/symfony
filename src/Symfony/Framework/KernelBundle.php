<?php

namespace Symfony\Framework;

use Symfony\Framework\Bundle\Bundle;
use Symfony\Framework\ClassCollectionLoader;
use Symfony\Framework\DependencyInjection\KernelExtension;
use Symfony\Components\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Components\DependencyInjection\ContainerInterface;
use Symfony\Components\DependencyInjection\Loader\Loader;
use Symfony\Components\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Components\DependencyInjection\BuilderConfiguration;

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * KernelBundle.
 *
 * @package    Symfony
 * @subpackage Framework
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class KernelBundle extends Bundle
{
    /**
     * Customizes the Container instance.
     *
     * @param \Symfony\Components\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag A ParameterBagInterface instance
     *
     * @return \Symfony\Components\DependencyInjection\BuilderConfiguration A BuilderConfiguration instance
     */
    public function buildContainer(ParameterBagInterface $parameterBag)
    {
        Loader::registerExtension(new KernelExtension());

        $configuration = new BuilderConfiguration();

        $loader = new XmlFileLoader(array(__DIR__.'/../Resources/config', __DIR__.'/Resources/config'));
        $configuration->merge($loader->load('services.xml'));

        if ($parameterBag->get('kernel.debug')) {
            $configuration->merge($loader->load('debug.xml'));
            $configuration->setDefinition('event_dispatcher', $configuration->findDefinition('debug.event_dispatcher'));
        }

        return $configuration;
    }

    /**
     * Boots the Bundle.
     *
     * @param Symfony\Components\DependencyInjection\ContainerInterface $container A ContainerInterface instance
     */
    public function boot(ContainerInterface $container)
    {
        $container->getErrorHandlerService();

        // load core classes
        if ($container->getParameter('kernel.include_core_classes')) {
            ClassCollectionLoader::load($container->getParameter('kernel.compiled_classes'), $container->getParameter('kernel.cache_dir'), 'classes', $container->getParameter('kernel.debug'));
        }
    }
}
