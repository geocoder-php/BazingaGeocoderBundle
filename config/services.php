<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Bazinga\GeocoderBundle\Command\GeocodeCommand;
use Bazinga\GeocoderBundle\Mapping\Driver\AttributeDriver;
use Bazinga\GeocoderBundle\Mapping\Driver\ChainDriver;
use Bazinga\GeocoderBundle\Mapping\Driver\DriverInterface;
use Bazinga\GeocoderBundle\Plugin\FakeIpPlugin;
use Bazinga\GeocoderBundle\Validator\Constraint\AddressValidator;
use Geocoder\Dumper\Dumper;
use Geocoder\Dumper\GeoArray;
use Geocoder\Dumper\GeoJson;
use Geocoder\Dumper\Gpx;
use Geocoder\Dumper\Kml;
use Geocoder\Dumper\Wkb;
use Geocoder\Dumper\Wkt;
use Geocoder\ProviderAggregator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->instanceof(Dumper::class)
        ->public();

    $services
        ->set(GeoArray::class)
        ->set(GeoJson::class)
        ->set(Gpx::class)
        ->set(Kml::class)
        ->set(Wkb::class)
        ->set(Wkt::class)

        ->load('Bazinga\\GeocoderBundle\\ProviderFactory\\', __DIR__.'/../src/ProviderFactory')
            ->autowire()
            ->autoconfigure()

        ->set(ProviderAggregator::class)

        ->set(FakeIpPlugin::class)
            ->args([null, null, false])

        ->set(GeocodeCommand::class)
            ->args([
                service(ProviderAggregator::class),
            ])
            ->tag('console.command')

        ->set(AddressValidator::class)
            ->args([
                service(ProviderAggregator::class),
            ])
            ->tag('validator.constraint_validator')

        ->set(ChainDriver::class)
            ->args([
                tagged_iterator('bazinga_geocoder.metadata.driver', exclude: [ChainDriver::class]),
            ])
            ->tag('bazinga_geocoder.metadata.driver')
        ->alias(DriverInterface::class, ChainDriver::class)

        ->set(AttributeDriver::class)
            ->tag('bazinga_geocoder.metadata.driver')
    ;
};
