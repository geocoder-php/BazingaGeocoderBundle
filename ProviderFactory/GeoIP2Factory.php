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

use Geocoder\Provider\GeoIP2\GeoIP2;
use Geocoder\Provider\GeoIP2\GeoIP2Adapter;
use Geocoder\Provider\Provider;
use GeoIp2\Database\Reader;
use GeoIp2\ProviderInterface;
use GeoIp2\WebService\Client;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GeoIP2Factory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => GeoIP2::class, 'packageName' => 'geocoder-php/geoip2-provider'],
    ];

    protected function getProvider(array $config): Provider
    {
        $provider = $config['provider'];
        if ('webservice' === $provider) {
            $provider = new Client($config['user_id'], $config['license_key'], $config['locales'], $config['webservice_options']);
        } elseif ('database' === $provider) {
            $provider = new Reader($config['database_filename'], $config['locales']);
        } else {
            $provider = $config['provider_service'];
        }

        $adapter = new GeoIP2Adapter($provider, $config['model']);

        return new GeoIP2($adapter);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'model' => GeoIP2Adapter::GEOIP2_MODEL_CITY,
            'database_filename' => null,
            'user_id' => null,
            'license_key' => null,
            'webservice_options' => [],
            'locales' => ['en'],
            'provider_service' => null,
        ]);

        $resolver->setRequired('provider');
        $resolver->setAllowedTypes('provider', ['string']);
        $resolver->setAllowedTypes('provider_service', [ProviderInterface::class, 'null']);
        $resolver->setAllowedTypes('model', ['string']);
        $resolver->setAllowedTypes('user_id', ['string', 'null']);
        $resolver->setAllowedTypes('license_key', ['string', 'null']);
        $resolver->setAllowedTypes('locales', ['array']);
        $resolver->setAllowedTypes('webservice_options', ['array']);
        $resolver->setAllowedTypes('database_filename', ['string', 'null']);

        $resolver->setAllowedValues('model', [GeoIP2Adapter::GEOIP2_MODEL_CITY, GeoIP2Adapter::GEOIP2_MODEL_COUNTRY]);
        $resolver->setAllowedValues('provider', ['webservice', 'database', 'service']);
    }
}
