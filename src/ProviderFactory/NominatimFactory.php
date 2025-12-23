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
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class NominatimFactory extends AbstractFactory
{
    protected static array $dependencies = [
        ['requiredClass' => Nominatim::class, 'packageName' => 'geocoder-php/nominatim-provider'],
    ];

    /**
     * @param array{root_url: string, user_agent: string, http_client: ?ClientInterface} $config
     */
    protected function getProvider(array $config): Provider
    {
        $httpClient = $config['http_client'] ?? $this->httpClient ?? Psr18ClientDiscovery::find();

        return new Nominatim($httpClient, $config['root_url'], $config['user_agent']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'http_client' => null,
            'root_url' => 'https://nominatim.openstreetmap.org',
            'user_agent' => 'BazingaGeocoderBundle',
        ]);

        $resolver->setAllowedTypes('http_client', ['object', 'null']);
        $resolver->setAllowedTypes('root_url', ['string']);
        $resolver->setAllowedTypes('user_agent', ['string']);
        $resolver->setRequired('user_agent');
    }
}
