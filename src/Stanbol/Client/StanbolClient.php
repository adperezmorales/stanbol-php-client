<?php

namespace Stanbol\Client;

use Stanbol\Http\ClientFactory;
use Stanbol\Http\ClientFactoryInterface;
use Stanbol\Services\StanbolService;
use Stanbol\Services\Enhancer\StanbolEnhancerService;
use Stanbol\Services\Enhancer\StanbolEnhancerServiceImpl;
use Stanbol\Services\Entityhub\StanbolEntityhubService;
use Stanbol\Services\Entityhub\StanbolEntityhubServiceImpl;
use Stanbol\Client\Exception\StanbolClientUrlException;

/**
 * <p>Stanbol Client Class</p>
 * <p>Class used to generate the different Client services to the Apache Stanbol Components</p>
 * TODO create the StanbolClient interface
 * @author Antonio David Pérez Morales <adperezmorales@gmail.com>
 */
class StanbolClient
{

    /**
     * <p>The Stanbol Endpoint</p>
     * @var string
     */
    private $stanbolEndpoint;

    /**
     * <p>The Stanbol Client configuration</p>
     * @var mixed
     */
    private $config;

    /**
     * <p>The Http client (Guzzle client)</p>
     * 
     * @var \Guzzle\Http\ClientInterface
     */
    private $httpClient;

    /**
     * <p>The <code>\Stanbol\Util\Http\ClientFactoryInterface</code> object to use to generate the Http clients</p>
     * 
     * @var \Stanbol\Util\Http\ClientFactoryInterface
     */
    private $clientFactory;

    /* STATIC ATTRIBUTES */

    /**
     * <p>Contains the instances of several Stanbol clients</p>
     * @var mixed
     */
    private static $instances;
    
    
    /* END STATIC ATTRIBUTES */
    
    /* STANBOL SERVICES */
    /**
     * <p>The <code>\Stanbol\Services\Enhancer\StanbolEnhancerService</code> instance</p>
     * 
     * @var \Stanbol\Services\Enhancer\StanbolEnhancerService
     */
    private $enhancerService;
    
    /**
     * <p>The <code>\Stanbol\Services\Entityhub\StanbolEntityhubService</code> instance</p>
     * @var type 
     */
    private $entityhubService;

    /* END STANBOL SERVICES */
    

    /**
     * <p>Stanbol Client Constructor</p>
     * <p>Generates an internal Http client using the Stanbol Endpoint and the configuration</p>
     * <p>In order to generate the Http client, it uses a default <code>\Stanbol\Util\Http\ClientFactoryInterface</code> instance or the one passed as parameter</p>
     * 
     * @param mixed $stanbolEndpoint The endpoint of the Stanbol server
     * @param array $config The optional configuration array to be used
     * @param \Stanbol\Util\Http\ClientFactortInterface $clientFactory the optional ClientFactoryInterface object to be used to generate the Http Clients
     */
    private function __construct($stanbolEndpoint, $config = array(), ClientFactoryInterface $clientFactory = null)
    {
        assert('!empty($stanbolEndpoint)');
        $this->stanbolEndpoint = $stanbolEndpoint;
        $this->config = $config;
        $this->clientFactory = $clientFactory == null ? ClientFactory::getInstance() : $clientFactory;
        $this->setHttpClient($this->clientFactory->create($this->stanbolEndpoint, $this->config));
    }

    /**
     * <p>Sets the Http client to use</p>
     * @param mixed $httpClient The Http Client to use
     */
    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
        $this->reloadHttpClientServices();
    }

    /**
     * <p>Gets the Http client to be used</p>
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * <p>Gets the configuration of this client</p>
     * <p>This configuration is used to create the Http clients to be used by this client</p>
     * 
     * @return mixed The configuration being used
     */
    public function getConfiguration()
    {
        return $this->config;
    }

    /**
     * <p>Sets the configuration of this client</p>
     * <p>This configuration is used to create the Http clients to be used by this client</p>
     * 
     * @param mixed $configuration The configuration to be used
     * @param boolean $addToHttpClient Flag to indicate whether the new configuration must be set in the Http Client
     */
    public function setConfiguration($configuration, $addToHttpClient = false)
    {
        $this->config = $configuration;
        if ($addToHttpClient) {
            $this->httpClient->setConfig($this->config);
            $this->reloadHttpClientServices();
        }
    }

    /**
     * <p>Update the services in order to use the Http client contained in this class</p>
     */
    private function reloadHttpClientServices()
    {
        if ($this->enhancerService != null)
            $this->enhancerService->setHttpClient($this->httpClient);
        if($this->entityhubService != null)
            $this->entityhubService->setHttpClient($this->httpClient);
    }

    /**
     * <p>Gets an instance of Stanbol Client for the specified endpoint</p>
     * 
     * @param string $stanbolEndpoint The Stanbol endpoint
     * @param array $config The optional configuration array
     */
    public static function getInstance($stanbolEndpoint, $config = array())
    {

        if (!filter_var($stanbolEndpoint, FILTER_VALIDATE_URL))
            throw new \Stanbol\Exception\StanbolClientUrlException("Supplied Stanbol Endpoint [$stanbolEndpoint] is not a valid URL");

        /*
         * Creates a hash using the stanbol endpoint and the supplied config
         */
        $stanbolClientHash = md5($stanbolEndpoint . serialize($config));
        if (!isset(self::$instances[$stanbolClientHash])) {
            self::$instances[$stanbolClientHash] = new self($stanbolEndpoint, $config);
        }

        return self::$instances[$stanbolClientHash];
    }

    /*
     * Stanbol Client Methods
     */

    /**
     * <p>Gets the instance for the Stanbol Enhancer Service</p>
     * 
     * @return \Stanbol\Services\StanbolEnhancerService The instance of the Stanbol Enhancer Service
     */
    public function enhancer()
    {
        if ($this->enhancerService == null)
            $this->enhancerService = new StanbolEnhancerServiceImpl($this->httpClient);

        return $this->enhancerService;
    }

    /**
     * Stanbol entityhub service
     *
     * @return \Stanbol\Services\Entityhub\StanbolEntityhubService service
     */
    public function entityhub()
    {
        if($this->entityhubService == null)
            $this->entityhubService = new StanbolEntityhubServiceImpl($this->httpClient);
        
        return $this->entityhubService;
    }

}

?>
