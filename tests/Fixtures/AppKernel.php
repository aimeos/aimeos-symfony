<?php

namespace Aimeos\ShopBundle\Tests\Fixtures;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;


class AppKernel extends Kernel
{
	/**
	 * {@inheritdoc}
	 */
	public function registerBundles() : \Traversable|array
	{
		return array(
			new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
			new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
			new \Symfony\Bundle\MonologBundle\MonologBundle(),
			new \Symfony\Bundle\TwigBundle\TwigBundle(),
			new \Aimeos\ShopBundle\AimeosShopBundle(),
		);
	}


	/**
	 * {@inheritdoc}
	 */
	public function registerContainerConfiguration( LoaderInterface $loader )
	{
		$loader->load( __DIR__ . '/config/config.yaml' );
	}


	/**
	 * {@inheritdoc}
	 */
	public function getCacheDir() : string
	{
		return __DIR__ . '/var/cache';
	}


	/**
	 * {@inheritdoc}
	 */
	public function getLogDir() : string
	{
		return __DIR__ . '/var/logs';
	}
}
