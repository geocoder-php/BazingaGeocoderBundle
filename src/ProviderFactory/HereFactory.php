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

use Geocoder\Provider\Here\Here;
use Geocoder\Provider\Provider;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class HereFactory extends AbstractFactory
{
    protected static array $dependencies = [
        ['requiredClass' => Here::class, 'packageName' => 'geocoder-php/here-provider'],
    ];

    /**
     * @param array{app_key: ?string, app_id: ?string, app_code: ?string, use_cit: bool, http_client: ?ClientInterface} $config
     */
    protected function getProvider(array $config): Provider
    {
        if (empty($config['app_key']) && empty($config['app_id']) && empty($config['app_code'])) {
            throw new \InvalidArgumentException('No authentication key provided. Here requires app_key or app_code and app_id.');
        }

        $httpClient = $config['http_client'] ?? $this->httpClient ?? Psr18ClientDiscovery::find();

        if (!empty($config['app_key'])) {
            return Here::createUsingApiKey($httpClient, $config['app_key'], $config['use_cit']);
        }

        return new Here($httpClient, $config['app_id'], $config['app_code'], $config['use_cit']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'http_client' => null,
            'use_cit' => false,
            'app_key' => null,
            'app_id' => null,
            'app_code' => null,
        ]);

        $resolver->setAllowedTypes('http_client', ['object', 'null']);
        $resolver->setAllowedTypes('app_key', ['string', 'null']);
        $resolver->setAllowedTypes('app_id', ['string', 'null']);
        $resolver->setAllowedTypes('app_code', ['string', 'null']);
        $resolver->setAllowedTypes('use_cit', ['bool', 'false']);
    }
}
