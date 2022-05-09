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

use Geocoder\Provider\MaxMindBinary\MaxMindBinary;
use Geocoder\Provider\Provider;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MaxMindBinaryFactory extends AbstractFactory
{
    protected static array $dependencies = [
        ['requiredClass' => MaxMindBinary::class, 'packageName' => 'geocoder-php/maxmind-binary-provider'],
    ];

    /**
     * @phpstan-param array{dat_file: string, open_flag: ?int} $config
     */
    protected function getProvider(array $config): Provider
    {
        return new MaxMindBinary($config['dat_file'], $config['open_flag']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'open_flag' => null,
        ]);

        $resolver->setRequired('dat_file');
        $resolver->setAllowedTypes('dat_file', ['string']);
        $resolver->setAllowedTypes('open_flag', ['int', 'null']);
    }
}
