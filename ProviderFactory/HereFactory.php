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
use Http\Discovery\HttpClientDiscovery;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class HereFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => Here::class, 'packageName' => 'geocoder-php/here-provider'],
    ];

    protected function getProvider(array $config): Provider
    {
        $httplug = $config['httplug_client'] ?: $this->httpClient ?? HttpClientDiscovery::find();

        if (empty($config['app_key']) && empty($config['app_id']) && empty($config['app_code'])) {
            throw new \InvalidArgumentException('No authentication key provided. Here requires app_key or app_code and app_id.');
        }

        if (!empty($config['app_key'])) {
            if (!method_exists(Here::class, 'createUsingApiKey')) {
                throw new \InvalidArgumentException('Here provider has no support for `creatingUsingApiKey` method.');
            }

            return Here::createUsingApiKey($httplug, $config['app_key'], $config['use_cit']);
        }

        return new Here($httplug, $config['app_id'], $config['app_code'], $config['use_cit']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'httplug_client' => null,
            'use_cit' => false,
            'app_key' => null,
            'app_id' => null,
            'app_code' => null,
        ]);

        $resolver->setAllowedTypes('httplug_client', ['object', 'null']);
        $resolver->setAllowedTypes('app_key', ['string', 'null']);
        $resolver->setAllowedTypes('app_id', ['string', 'null']);
        $resolver->setAllowedTypes('app_code', ['string', 'null']);
        $resolver->setAllowedTypes('use_cit', ['bool', 'false']);
    }
}
