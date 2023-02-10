<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MinkorrektExtension extends Extension
{
    public function load(array $mergedConfig, ContainerBuilder $containerBuilder): void
    {
        $yamlFileLoader = new YamlFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $yamlFileLoader->load("services.yaml");
        $yamlFileLoader->load("commands.yaml");


    }

}