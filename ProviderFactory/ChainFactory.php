<?php

/*
 * This file is part of php-cache organization.
 *
 * (c) 2015-2015 Aaron Scherer <aequasi@gmail.com>, Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cache\AdapterBundle\Factory;

use Bazinga\Bundle\GeocoderBundle\ProviderFactory\AbstractFactory;
use Geocoder\Provider\Chain\Chain;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class ChainFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => Chain::class, 'packageName' => 'geocoder-php/chain-provider'],
    ];

    /**
     * {@inheritdoc}
     */
    protected function getProvider(array $config)
    {
        return new Chain($config['services']);
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
