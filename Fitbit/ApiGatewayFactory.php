<?php
/**
 *
 * Error Codes: 101 - 112
 */
namespace Nibynool\FitbitInterfaceBundle\Fitbit;

use OAuth\Common\Consumer\Credentials;
use OAuth\ServiceFactory;
use OAuth\OAuth2\Service\FitBit as ServiceInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Client\ClientInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Stopwatch\Stopwatch;
use Nibynool\FitbitInterfaceBundle\Fitbit\Exception as FBException;

/**
 * Class ApiGatewayFactory
 *
 * @package Nibynool\FitbitInterfaceBundle\Fitbit
 *
 * @since 0.1.0
 * @version 0.5.0
 *
 * @method AuthenticationGateway getAuthenticationGateway()
 * @method ActivityGateway getActivityGateway()
 * @method ActivityStatsGateway getActivityStatsGateway()
 * @method ActivityTimeSeriesGateway getActivityTimeSeriesGateway()
 * @method BodyGateway getBodyGateway()
 * @method BodyTimeSeriesGateway getBodyTimeSeriesGateway()
 * @method FoodGateway getFoodGateway()
 * @method FoodTimeSeriesGateway getFoodTimeSeriesGateway()
 * @method GoalGateway getGoalGateway()
 * @method SleepGateway getSleepGateway()
 * @method SleepTimeSeriesGateway getSleepTimeSeriesGateway()
 * @method TimeGateway getTimeGateway()
 * @method TrackerGateway getTrackerGateway()
 * @method UserGateway getUserGateway()
 * @method WaterGateway getWaterGateway()
 */
class ApiGatewayFactory
{
    /**
     * @var string
     */
    protected $consumerKey;
    /**
     * @var string
     */
    protected $consumerSecret;
    /**
     * @var string (default: '-')
     */
    protected $userID = '-';
    /**
     * @var string (default: 'json')
     */
    protected $responseFormat = 'json';
    /**
     * @var ServiceInterface
     */
    protected $service;
    /**
     * @var TokenStorageInterface
     */
    protected $storageAdapter;
    /**
     * @var string
     */
    protected $callbackURL;
    /**
     * @var array
     */
    protected $scopes;
    /**
     * @var ClientInterface
     */
    protected $httpClient;
	/**
	 * @var array
	 */
	protected $configuration;
	/**
	 * @var Router
	 */
	protected $router;

	/**
	 * Set the consumer credentials when this class is instantiated
	 *
	 * @access public
	 *
	 * @param string $consumer_key Application consumer key for Fitbit API
	 * @param string $consumer_secret Application secret
	 * @param string $callback_url Callback URL to provide to Fitbit
	 * @param array  $configuration Configurable items
	 * @param Router $router
	 */
	public function __construct($consumer_key, $consumer_secret, $callback_url, array $scopes, $configuration, Router $router)
	{
		$this->consumerKey    = $consumer_key;
		$this->consumerSecret = $consumer_secret;
		$this->callbackURL    = $callback_url;
		$this->configuration  = $configuration;
		$this->router         = $router;
        $this->scopes         = $scopes;
	}

	/**
     * Set consumer credentials
     * 
     * @access public
	 *
     * @param string $consumer_key Application consumer key for Fitbit API
     * @param string $consumer_secret Application secret
     * @return self
     */
    public function setCredentials($consumer_key, $consumer_secret)
    {
        $this->consumerKey    = $consumer_key;
        $this->consumerSecret = $consumer_secret;
        return $this;
    }

    /**
     * Set storage adapter.
     * 
     * @access public
     *
     * @param TokenStorageInterface $adapter
     * @return self
     */
    public function setStorageAdapter(TokenStorageInterface $adapter)
    {
        $this->storageAdapter = $adapter;
        return $this;
    }

    /**
     * Get storage adapter.
     * 
     * @access public
     *
     * @return TokenStorageInterface
     */
    public function getStorageAdapter()
    {
        return $this->storageAdapter;
    }

    /**
     * Set response format.
     *
     * @access public
     * @version 0.5.0
     *
     * @param string $format Response format (json or xml) to use in API calls
     * @throws FBException
     * @return self
     */
    public function setResponseFormat($format)
    {
        if (!in_array($format, array('json', 'xml'))) throw new FBException('Response format can only be set to \'json\' or \'xml\'.', 101);
        $this->responseFormat = $format;
        return $this;
    }

    /**
     * Set callback URL.
     * 
     * @access public
     * @version 0.5.0
     *
     * @param string $url
     * @throws FBException
     * @return self
     */
    public function setCallbackURL($url)
    {
	    if(substr($url, 0, 1) == '/' && substr($url, 0, 2) != '//') $url = $this->router->getContext()->getBaseUrl().$url;
	    if (!filter_var($url, FILTER_VALIDATE_URL)) throw new FBException('The provided callback URL ('.$url.') is not a valid URL.', 102);
        $this->callbackURL = $url;
        return $this;
    }

    /**
     * Set Fitbit user id for API calls
     *
     * @access public
     *
     * @param string $id
     * @return self
     */
    public function setUserID($id)
    {
        $this->userID = $id;
        return $this;
    }

    /**
     * Set HTTP Client library for Fitbit service.
     *
     * @access public
     *
     * @param  ClientInterface $client
     * @return self
     */
    public function setHttpClient(ClientInterface $client)
    {
        $this->httpClient = $client;
        return $this;
    }

	/**
	 * Open a Gateway
	 *
	 * @access public
	 * @version 0.5.2
	 *
	 * @param $method
	 * @param $parameters
	 * @throws Exception
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		/** @var Stopwatch $timer */
		$timer = new Stopwatch();
		$timer->start('Establishing Gateway', 'Fitbit API');
		if (!preg_match('/^get.*Gateway$/', $method)) throw new FBException('Invalid API Gateway interface ('.$method.') requested.', 103);
		if (count($parameters)) throw new FBException('API Gateway interfaces do not accept parameters.', 104);
		$gatewayName = '\\'.__NAMESPACE__.'\\'.substr($method, 3);
		try
		{
			$gateway = new $gatewayName($this->configuration);
		}
		catch (\Exception $e)
		{
			$timer->stop('Establishing Gateway');
			throw new FBException('API Gateway could not open a gateway named '.$gatewayName.'.', 105);
		}
		$this->injectGatewayDependencies($gateway);
		$timer->stop('Establishing Gateway');
		return $gateway;
	}

	/**
	 * Inject Dependencies into a Gateway Interface
	 *
	 * @access protected
	 * @version 0.5.0
	 *
	 * @param EndpointGateway $gateway
	 * @throws FBException
	 * @return bool
	 */
	protected function injectGatewayDependencies(EndpointGateway $gateway)
    {
	    try
	    {
		    $gateway->setService($this->getService())
			    ->setResponseFormat($this->responseFormat)
			    ->setUserID($this->userID);
	    }
	    catch (\Exception $e)
	    {
		    throw new FBException('Could not inject gateway dependencies', 112, $e);
	    }
	    return true;
    }

    /**
     * Get Fitbit service
     *
     * @access protected
     * @version 0.5.0
     *
     * @throws FBException
     * @return ServiceInterface
     */
    protected function getService()
    {
        if (!$this->consumerKey)    throw new FBException('Cannot get service as the consumer key is empty.', 106);
        if (!$this->consumerSecret) throw new FBException('Cannot get service as the consumer secret is empty.', 107);
        if (!$this->callbackURL)    throw new FBException('Cannot get service as the callback URL is empty.', 108);
        if (!$this->storageAdapter) throw new FBException('Cannot get service as it is missing a storage adapter.', 109);

        if (!$this->service)
        {
            try
            {
	            $credentials = new Credentials(
		            $this->consumerKey,
		            $this->consumerSecret,
		            $this->callbackURL
	            );
            }
            catch (\Exception $e)
            {
	            throw new FBException('Could not initialise the credentials.', 110, $e);
            }

	        try
	        {
	            $factory = new ServiceFactory();
		        if ($this->httpClient) $factory->setHttpClient($this->httpClient);
		        $this->service = $factory->createService('FitBit', $credentials, $this->storageAdapter, $this->scopes);
	        }
	        catch (\Exception $e)
	        {
		        throw new FBException('Could not initialise service factory.', 111, $e);
	        }
        }
        return $this->service;
    }
}
