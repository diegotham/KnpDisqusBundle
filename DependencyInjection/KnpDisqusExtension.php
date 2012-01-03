<?php

/*
* This file is part of the KnpDisqusBundle package.
*
* (c) KnpLabs <hello@knplabs.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Knp\Bundle\DisqusBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

class KnpDisqusExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration($container->getParameter('kernel.debug'));
        $config = $processor->processConfiguration($configuration, $configs);

        if ($container->hasParameter('knp_zend_cache')) {
            foreach ($config['forums'] as $shortname => $data) {
                if (isset($data['cache'])) {
                    if (!$container->hasParameter('knp_zend_cache.templates.'.$data['cache'])) {
                        throw new \InvalidArgumentException('Unknown cache template key used: '.$data['cache']);
                    }

                    $container->setParameter('knp_disqus.cache.'.$shortname, $data['cache']);
                }
            }
        }

        $container->setParameter('knp_disqus.api_key', $config['api_key']);
        $container->setParameter('knp_disqus.debug', $config['debug']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
