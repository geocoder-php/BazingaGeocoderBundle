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

use Geocoder\Provider\Chain\Chain;
use Geocoder\Provider\Provider;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class ChainFactory extends AbstractFactory implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected static $dependencies = [
        ['requiredClass' => Chain::class, 'packageName' => 'geocoder-php/chain-provider'],
    ];

    /**
     * {@inheritdoc}
     */
    protected function getProvider(array $config): Provider
    {
        $provider = new Chain($config['services']);
        if (null !== $this->logger) {
            $provider->setLogger($this->logger);
        }

        return $provider;
    }

    /**
     * {@inheritdoc}
     */
    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        parent::configureOptionResolver($resolver);

        $resolver->setRequired('services');
        $resolver->setAllowedTypes('services', ['array']);
    }
}
