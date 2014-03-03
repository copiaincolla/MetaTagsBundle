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

                ->arrayNode('config')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('allow_editable_url')->defaultFalse()->end()
                        ->arrayNode('truncate_function')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('limit')->defaultValue(180)->end()
                                ->scalarNode('truncateWords')->defaultFalse()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('urls_loader')
                    ->addDefaultsIfNotSet()
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

                        ->arrayNode('custom_service')
                            ->children()
                                ->scalarNode('id')->end()
                                ->scalarNode('function')->defaultNull()->end()
                            ->end()
                        ->end()

                        ->arrayNode('parameters')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('fixed_params')
                                    ->defaultValue(array())
                                ->end()
                                ->arrayNode('dynamic_routes')
                                    ->useAttributeAsKey('route_name')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('repository')->defaultNull()->end()

                                            ->scalarNode('repository_fetch_function')->defaultNull()->end()

                                            ->variableNode('fixed_params')->defaultValue(array())->end()

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
                ->end()

            ->end()
        ;

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}