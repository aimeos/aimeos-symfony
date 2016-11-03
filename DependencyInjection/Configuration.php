<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2016
 * @package symfony
 * @subpackage DependencyInjection
 */


namespace Aimeos\ShopBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 * @package symfony
 * @subpackage DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root( 'aimeos_shop' );

		$rootNode
			->children()
				->booleanNode('disable_sites')->defaultValue( true )->end()
				->booleanNode('apc_enable')->defaultValue( false )->end()
				->scalarNode('apc_prefix')->defaultValue( 'sf2:' )->end()
				->scalarNode('extdir')->end()
				->scalarNode('uploaddir')->end()
				->variableNode('admin')->defaultValue( array() )->end()
				->variableNode('client')->defaultValue( array() )->end()
				->variableNode('controller')->defaultValue( array() )->end()
				->variableNode('i18n')->defaultValue( array() )->end()
				->variableNode('madmin')->defaultValue( array() )->end()
				->variableNode('mshop')->defaultValue( array() )->end()
				->variableNode('resource')->defaultValue( array() )->end()
				->variableNode('page')->defaultValue( array() )->end()
				->variableNode('backend')->defaultValue( array() )->end()
				->variableNode('frontend')->defaultValue( array() )->end()
				->variableNode('command')->defaultValue( array() )->end()
			->end()
		;

		return $treeBuilder;
	}
}
