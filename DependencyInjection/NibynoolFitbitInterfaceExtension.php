<?php

namespace Nibynool\FitbitInterfaceBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

class NibynoolFitbitInterfaceExtension extends Extension
{
	public function load(array $configs, ContainerBuilder $container)
	{
		$loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
		$loader->load('services.yml');

		$configuration = new Configuration();
		$config = $this->processConfiguration($configuration, $configs);
		$container->setParameter('nibynool_fitbit_interface.key',      $config['key']);
		$container->setParameter('nibynool_fitbit_interface.secret',   $config['secret']);
		$container->setParameter('nibynool_fitbit_interface.callback', $config['callback']);
                $container->setParameter('nibynool_fitbit_interface.scopes',   $config['scopes']);
		$container->setParameter(
			'nibynool_fitbit_interface.configuration',
			array(
				'distance_units'                => $config['distance_units'],
				'interday_timeseries_endpoints' => $config['interday_timeseries_endpoints'],
				'subscription_types'            => $config['subscription_types'],
				'water_units'                   => $config['water_units']
			)
		);
	}

	public function getAlias()
	{
		return 'nibynool_fitbit_interface';
	}
}
