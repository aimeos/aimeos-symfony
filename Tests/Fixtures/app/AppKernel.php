<?php


use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;


class AppKernel extends Kernel
{
	/**
	 * {@inheritdoc}
	 */
	public function registerBundles()
	{
		return array(
			new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
			new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
			new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
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
		$loader->load( __DIR__ . '/config/config.yml' );
	}


	/**
	 * {@inheritdoc}
	 */
	public function getCacheDir()
	{
		return sys_get_temp_dir() . '/aimeos-symfony/cache';
	}


	/**
	 * {@inheritdoc}
	 */
	public function getLogDir()
	{
		return sys_get_temp_dir() . '/aimeos-symfony/logs';
	}
}
