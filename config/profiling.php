<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Bazinga\GeocoderBundle\DataCollector\GeocoderDataCollector;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set(GeocoderDataCollector::class)
        ->tag('data_collector', ['template' => '@BazingaGeocoder/Collector/geocoder.html.twig', 'id' => 'geocoder']);
};
