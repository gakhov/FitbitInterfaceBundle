<?php

namespace NibyNool\FitBitBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('fitbit')
			->children()
				->scalarNode('key')
				->info('The FitBit API key')
			->end()
			->scalarNode('secret')
				->info('The FitBit API secret')
			->end()
			->scalarNode('callback')
				->info('The callback URL to pass to FitBit')
			->end()
			->arrayNode('distance_units')
				->addDefaultsIfNotSet()
				->requiresAtLeastOneElement()
				->info('Distance units recognised by FitBit')
				->defaultValue(array('Centimeter', 'Foot', 'Inch', 'Kilometer', 'Meter', 'Mile', 'Millimeter', 'Steps', 'Yards'))
			->end()
			->arrayNode('timeseries_endpoints')
				->addDefaultsIfNotSet()
				->requiresAtLeastOneElement()
		        ->info('Valid end points for FitBit time series data')
				->prototype('array')
					->children()
						->scalarNode('value')
							->isRequired()
						->end()
					->end()
				->end()
				->defaultValue(array(
					'caloriesIn'            => array('value' => '/foods/log/caloriesIn'),
                    'water'                 => array('value' => '/foods/log/water'),
                    'caloriesOut'           => array('value' => '/activities/log/calories'),
                    'steps'                 => array('value' => '/activities/log/steps'),
	                'distance'              => array('value' => '/activities/log/distance'),
	                'floors'                => array('value' => '/activities/log/floors'),
                    'elevation'             => array('value' => '/activities/log/elevation'),
                    'minutesSedentary'      => array('value' => '/activities/log/minutesSedentary'),
                    'minutesLightlyActive'  => array('value' => '/activities/log/minutesLightlyActive'),
                    'minutesFairlyActive'   => array('value' => '/activities/log/minutesFairlyActive'),
                    'minutesVeryActive'     => array('value' => '/activities/log/minutesVeryActive'),
                    'activeScore'           => array('value' => '/activities/log/activeScore'),
                    'activityCalories'      => array('value' => '/activities/log/activityCalories'),
                    'tracker_caloriesOut'   => array('value' => '/activities/log/tracker/calories'),
                    'tracker_steps'         => array('value' => '/activities/log/tracker/steps'),
                    'tracker_distance'      => array('value' => '/activities/log/tracker/distance'),
                    'tracker_floors'        => array('value' => '/activities/log/tracker/floors'),
                    'tracker_elevation'     => array('value' => '/activities/log/tracker/elevation'),
                    'tracker_activeScore'   => array('value' => '/activities/log/tracker/activeScore'),
                    'startTime'             => array('value' => '/sleep/startTime'),
                    'timeInBed'             => array('value' => '/sleep/timeInBed'),
                    'minutesAsleep'         => array('value' => '/sleep/minutesAsleep'),
                    'awakeningsCount'       => array('value' => '/sleep/awakeningsCount'),
                    'minutesAwake'          => array('value' => '/sleep/minutesAwake'),
                    'minutesToFallAsleep'   => array('value' => '/sleep/minutesToFallAsleep'),
                    'minutesAfterWakeup'    => array('value' => '/sleep/minutesAfterWakeup'),
                    'efficiency'            => array('value' => '/sleep/efficiency'),
                    'weight'                => array('value' => '/body/weight'),
                    'bmi'                   => array('value' => '/body/bmi'),
                    'fat'                   => array('value' => '/body/fat')
				))
		    ->end()
		->end();

		return $treeBuilder;
	}
}
