<?php

namespace Copiaincolla\MetaTagsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('copiaincolla_meta_tags')
            ->children()

                ->arrayNode('exposed_routes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('bundles')
                            ->useAttributeAsKey('k')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()

                ->scalarNode('urls_loader_custom_service')->defaultValue(null)->end()

                ->arrayNode('defaults')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('title')->defaultValue("")->end()
                        ->scalarNode('description')->defaultValue("")->end()
                        ->scalarNode('keywords')->defaultValue("")->end()
                        ->scalarNode('author')->defaultValue("")->end()
                    ->end()
                ->end()

                ->arrayNode('dynamic_routes')
                    ->children()
                        ->arrayNode('default_params')
                            ->useAttributeAsKey('k')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('routes')
                            ->useAttributeAsKey('route_name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('repository')
                                        ->defaultValue(null)
                                    ->end()

                                    ->scalarNode('repository_fetch_function')
                                        ->defaultValue(null)
                                    ->end()

                                    ->variableNode('fixed_params')
                                        ->defaultValue(array())
                                    ->end()

                                    ->arrayNode('object_params')
                                        ->useAttributeAsKey('k')
                                        ->prototype('scalar')->end()
                                    ->end()

                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

            ->end()
        ;

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
