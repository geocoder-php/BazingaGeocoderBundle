<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\ProviderFactory;

use Geocoder\Provider\Nominatim\Nominatim;
use Geocoder\Provider\Provider;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class NominatimFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => Nominatim::class, 'packageName' => 'geocoder-php/nominatim-provider'],
    ];

    protected function getProvider(array $config): Provider
    {
        $httplug = $config['httplug_client'] ?: HttpClientDiscovery::find();

        return new Nominatim($httplug, $config['root_url']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'httplug_client' => null,
            'root_url' => 'https://nominatim.openstreetmap.org',
        ]);

        $resolver->setAllowedTypes('httplug_client', [HttpClient::class, 'null']);
        $resolver->setAllowedTypes('root_url', ['string']);
    }
}
