<?php

namespace Nibynool\FitbitInterfaceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('nibynool_fitbit_interface')
			->children()
				->scalarNode('key')
					->info('The Fitbit API key')
				->end()
				->scalarNode('secret')
					->info('The Fitbit API secret')
				->end()
				->scalarNode('callback')
					->info('The callback URL to pass to Fitbit')
				->end()
                ->arrayNode('scopes')
					->requiresAtLeastOneElement()
                    ->info('The scopes array for FitBit API ')
                    ->prototype('scalar')->end()
                ->end()
				->arrayNode('distance_units')
					->requiresAtLeastOneElement()
					->info('Distance units recognised by Fitbit')
					->defaultValue(array('Centimeter', 'Foot', 'Inch', 'Kilometer', 'Meter', 'Mile', 'Millimeter', 'Steps', 'Yards'))
					->prototype('scalar')->end()
				->end()
				->arrayNode('interday_timeseries_endpoints')
					->requiresAtLeastOneElement()
			        ->info('Valid end points for Fitbit interday time series data')
					->prototype('array')
						->children()
							->scalarNode('value')
								->isRequired()
							->end()
						->end()
					->end()
					->defaultValue(
						array(
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
						)
					)
	            ->end()
				->arrayNode('intraday_timeseries_endpoints')
					->requiresAtLeastOneElement()
					->info('Valid end points for Fitbit intraday time series data')
					->prototype('array')
						->children()
							->scalarNode('value')
								->isRequired()
							->end()
						->end()
					->end()
					->defaultValue(
						array(
							'caloriesOut' => array('value' => '/activities/log/calories'),
							'steps'       => array('value' => '/activities/log/steps'),
							'floors'      => array('value' => '/activities/log/floors'),
							'elevation'   => array('value' => '/activities/log/elevation')
						)
					)
				->end()
				->arrayNode('subscription_types')
					->requiresAtLeastOneElement()
					->info('Valid end subscription types')
					->prototype('array')
						->children()
							->scalarNode('value')
								->isRequired()
							->end()
						->end()
					->end()
					->defaultValue(
						array(
							'sleep'      => array('value' => '/sleep'),
							'body'       => array('value' => '/body'),
							'activities' => array('value' => '/activities'),
							'foods'      => array('value' => '/foods'),
							'all'        => array('value' => ''),
							''           => array('value' => '')
						)
					)
				->end()
				->arrayNode('water_units')
					->requiresAtLeastOneElement()
					->info('Water units recognised by Fitbit')
					->prototype('scalar')->end()
					->defaultValue(
						array(
							'ml',
							'fl oz',
							'cup'
						)
					)
				->end()
			->end();

		return $treeBuilder;
	}
}
