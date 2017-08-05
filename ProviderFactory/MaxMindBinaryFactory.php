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

use Geocoder\Provider\MaxMind\MaxMind;
use Geocoder\Provider\MaxMindBinary\MaxMindBinary;
use Geocoder\Provider\Provider;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MaxMindBinaryFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => MaxMindBinary::class, 'packageName' => 'geocoder-php/maxmind-binary-provider'],
    ];

    protected function getProvider(array $config): Provider
    {
        return new MaxMindBinary($config['dat_file'], $config['open_flag']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'open_flag' => null,
        ]);

        $resolver->setRequired('dat_file');
        $resolver->setAllowedTypes('dat_file', ['string']);
        $resolver->setAllowedTypes('open_flag', ['string', 'null']);
    }
}
