<?php
// src/ContaoManager/Plugin.php
namespace ItsBessner\Qrcode\ContaoManager;


use ItsBessner\Qrcode\QrcodeBundle;

class Plugin implements BundlePluginInterface // , RoutingPluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(QrcodeBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }

}
