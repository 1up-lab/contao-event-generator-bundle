<?php

declare(strict_types=1);

namespace Oneup\Contao\EventGeneratorBundle\ContaoManager;

use Contao\CalendarBundle\ContaoCalendarBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Kmielke\CalendarExtendedBundle\CalendarExtendedBundle;
use MenAtWork\MultiColumnWizardBundle\MultiColumnWizardBundle;
use Oneup\Contao\EventGeneratorBundle\OneupContaoEventGeneratorBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(OneupContaoEventGeneratorBundle::class)->setLoadAfter([
                ContaoCoreBundle::class,
                ContaoCalendarBundle::class,
                MultiColumnWizardBundle::class,
                CalendarExtendedBundle::class,
            ]),
        ];
    }
}
