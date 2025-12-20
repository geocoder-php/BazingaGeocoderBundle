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

use Geocoder\Provider\ArcGISOnline\ArcGISOnline;
use Geocoder\Provider\Provider;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ArcGISOnlineFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => ArcGISOnline::class, 'packageName' => 'geocoder-php/arcgis-online-provider'],
    ];

    /**
     * @param array{source_country: ?string, http_client: ?ClientInterface} $config
     */
    protected function getProvider(array $config): Provider
    {
        $httpClient = $config['http_client'] ?? $this->httpClient ?? Psr18ClientDiscovery::find();

        return new ArcGISOnline($httpClient, $config['source_country']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'http_client' => null,
            'source_country' => null,
        ]);

        $resolver->setAllowedTypes('http_client', ['object', 'null']);
        $resolver->setAllowedTypes('source_country', ['string', 'null']);
    }
}
