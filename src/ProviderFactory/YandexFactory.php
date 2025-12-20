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

use Geocoder\Provider\Provider;
use Geocoder\Provider\Yandex\Yandex;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class YandexFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => Yandex::class, 'packageName' => 'geocoder-php/yandex-provider'],
    ];

    /**
     * @param array{toponym: ?string, api_key: ?string, http_client: ?ClientInterface} $config
     */
    protected function getProvider(array $config): Provider
    {
        $httpClient = $config['http_client'] ?? $this->httpClient ?? Psr18ClientDiscovery::find();

        return new Yandex($httpClient, $config['toponym'], $config['api_key']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'http_client' => null,
            'toponym' => null,
            'api_key' => null,
        ]);

        $resolver->setAllowedTypes('http_client', ['object', 'null']);
        $resolver->setAllowedTypes('toponym', ['string', 'null']);
        $resolver->setAllowedTypes('api_key', ['string', 'null']);
    }
}
