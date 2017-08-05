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

use Geocoder\Provider\Geoip\Geoip;
use Geocoder\Provider\Provider;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GeoipFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => Geoip::class, 'packageName' => 'geocoder-php/geoip-provider'],
    ];

    protected function getProvider(array $config): Provider
    {
        return new Geoip();
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
    }
}
