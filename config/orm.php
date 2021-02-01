<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Bazinga\GeocoderBundle\Doctrine\ORM\GeocodeEntityListener;
use Bazinga\GeocoderBundle\Mapping\Driver\DriverInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set(GeocodeEntityListener::class)
            ->args([
                tagged_locator('bazinga_geocoder.provider'),
                service(DriverInterface::class),
            ])
            ->tag('doctrine.event_listener', ['event' => 'onFlush'])
    ;
};
