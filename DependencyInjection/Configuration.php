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
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bazinga_geocoder');

        $rootNode
            ->children()
            ->append($this->getProvidersNode())
            ->scalarNode('default_provider')->defaultNull()->end()
            ->arrayNode('fake_ip')
                ->beforeNormalization()
                ->ifString()
                    ->then(function ($value) { return array('ip' => $value); })
                ->end()
                ->treatFalseLike(array('enabled' => false))
                ->treatTrueLike(array('enabled' => true))
                ->treatNullLike(array('enabled' => true))
                ->children()
                    ->booleanNode('enabled')
                        ->defaultTrue()
                    ->end()
                    ->scalarNode('ip')->defaultNull()->end()
                    ->scalarNode('priority')->defaultValue(0)->end()
                ->end()
            ->end()
            ->scalarNode('adapter')->defaultValue('bazinga_geocoder.geocoder.default_adapter')->end()
        ;

        return $treeBuilder;
    }

    /**
     * @return ArrayNodeDefinition
     */
    private function getProvidersNode()
    {
        $treeBuilder = new TreeBuilder();
        $node        = $treeBuilder->root('providers');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('factory')->isRequired()->cannotBeEmpty()->end()
                    ->variableNode('options')->defaultValue([])->end()
                    ->scalarNode('cache')->defaultNull()->end()
                    ->scalarNode('cache_lifetime')->defaultNull()->end()
                    ->arrayNode('aliases')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

}
