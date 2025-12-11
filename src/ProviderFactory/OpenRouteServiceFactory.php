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

use Geocoder\Provider\OpenRouteService\OpenRouteService;
use Geocoder\Provider\Provider;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OpenRouteServiceFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => OpenRouteService::class, 'packageName' => 'geocoder-php/openrouteservice-provider'],
    ];

    /**
     * @param array{api_key: string, http_client: ?ClientInterface} $config
     */
    protected function getProvider(array $config): Provider
    {
        $httpClient = $config['http_client'] ?? $this->httpClient ?? Psr18ClientDiscovery::find();

        return new OpenRouteService($httpClient, $config['api_key']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'http_client' => null,
            'api_key' => null,
        ]);

        $resolver->setRequired('api_key');
        $resolver->setAllowedTypes('http_client', ['object', 'null']);
        $resolver->setAllowedTypes('api_key', ['string']);
    }
}
