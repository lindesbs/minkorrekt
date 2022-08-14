<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(KnpMenuBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}