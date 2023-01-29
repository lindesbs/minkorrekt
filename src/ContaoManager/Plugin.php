<?php declare(strict_types=1);

namespace lindesbs\minkorrekt\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use lindesbs\ContaoTools\ContaoToolsBundle;
use lindesbs\minkorrekt\MinkorrektBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(MinkorrektBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}