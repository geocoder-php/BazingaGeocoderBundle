<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\GeocoderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bazinga_geocoder');

        $rootNode
            ->children()
            ->scalarNode('fake_ip')->defaultNull()->end()
            ->arrayNode('adapter')
                ->children()
                    ->scalarNode('class')->defaultNull()->end()
                ->end()
            ->end()
            ->arrayNode('providers')
                ->children()
                ->arrayNode('bing_maps')
                    ->children()
                        ->scalarNode('api_key')
                            ->isRequired()->cannotBeEmpty()
                        ->end()
                        ->scalarNode('locale')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('ip_info_db')
                    ->children()
                        ->scalarNode('api_key')
                            ->isRequired()->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('yahoo')
                    ->children()
                        ->scalarNode('api_key')
                            ->isRequired()->cannotBeEmpty()
                        ->end()
                        ->scalarNode('locale')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
            ;

        return $treeBuilder;
    }
}
