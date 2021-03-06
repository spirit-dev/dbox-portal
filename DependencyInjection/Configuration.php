<?php
/**
 * Copyright (c) 2016. Spirit-Dev
 * Licensed under GPLv3 GNU License - http://www.gnu.org/licenses/gpl-3.0.html
 *    _             _
 *   /_`_  ._._/___/ | _
 * . _//_//// /   /_.'/_'|/
 *    /
 *
 * Since 2K10 until today
 *
 * Hex            53 70 69 72 69 74 2d 44 65 76
 *
 * By             Jean Bordat
 * Twitter        @Ji_Bay_
 * Mail           <bordat.jean@gmail.com>
 *
 * File           Configuration.php
 * Updated the    01/09/16 16:25
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface {
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('spirit_dev_d_box_portal');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
        ->children()
        ->arrayNode('app')
            ->children()
                ->scalarNode('admin_mail')->end()
                ->scalarNode('from_mail')->end()
                ->scalarNode('subject_prepend')->end()
            ->scalarNode('self_base_url')->defaultValue('none')->end()
            ->end()
        ->end()
        ->arrayNode('gitlab_api')
            ->children()
                ->scalarNode('url')->end()
                ->scalarNode('token')->end()
            ->end()
        ->end()
        ->arrayNode('jenkins_api')
            ->children()
                ->scalarNode('url')->end()
                ->scalarNode('protocol')->end()
            ->booleanNode('ssl_verify')->defaultTrue()->end()
                ->scalarNode('user')->end()
                ->scalarNode('token')->end()
            ->scalarNode('password')->end()
                ->scalarNode('path')->end()
                ->scalarNode('default_pipeline_name')->end()
            ->scalarNode('external_uri')->defaultValue('none')->end()
            ->scalarNode('web_hook_use_external')->defaultFalse()->end()
            ->end()
        ->end()
        ->arrayNode('redmine_api')
            ->children()
                ->scalarNode('url')->end()
                ->scalarNode('protocol')->end()
                ->integerNode('port')->end()
                ->scalarNode('token')->end()
            ->booleanNode('ssl_verify')->defaultTrue()->end()
                ->integerNode('role_dev')->end()
                ->integerNode('role_manager')->end()
                ->arrayNode('pm_modules')
                    ->prototype('scalar')->end()
                ->end()
                ->integerNode('bug_tracker')->end()
                ->integerNode('evol_tracker')->end()
                ->integerNode('test_tracker')->end()
                ->integerNode('qa_tracker')->end()
                ->integerNode('auth_source')->end()
            ->end()
        ->end()
        ->arrayNode('sonar_api')
            ->children()
                ->scalarNode('url')->end()
                ->scalarNode('username')->end()
                ->scalarNode('password')->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
