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
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class YandexFactory extends AbstractFactory
{
    protected static array $dependencies = [
        ['requiredClass' => Yandex::class, 'packageName' => 'geocoder-php/yandex-provider'],
    ];

    /**
     * @phpstan-param array{toponym: ?string, api_key: ?string, httplug_client: ?HttpClient} $config
     */
    protected function getProvider(array $config): Provider
    {
        $httplug = $config['httplug_client'] ?: $this->httpClient ?? HttpClientDiscovery::find();

        return new Yandex($httplug, $config['toponym'], $config['api_key']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'httplug_client' => null,
            'toponym' => null,
            'api_key' => null,
        ]);

        $resolver->setAllowedTypes('httplug_client', ['object', 'null']);
        $resolver->setAllowedTypes('toponym', ['string', 'null']);
        $resolver->setAllowedTypes('api_key', ['string', 'null']);
    }
}
