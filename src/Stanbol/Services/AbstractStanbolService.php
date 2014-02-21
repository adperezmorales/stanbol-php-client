<?php

namespace Stanbol\Services;

use Stanbol\Services\StanbolService;
use Stanbol\Services\Exception\StanbolServiceException;
use Stanbol\Http\ClientFactoryInterface;
use Stanbol\Http\ClientFactory;

/**
 * <p>Abstract Stanbol Service. Base class for Stanbol Services</p>
 * <p>Provides some methods and attributes shared by several Stanbol Services</p>
 *
 * 
 * @author Antonio David Perez Morales <adperezmorales@gmail.com>
 */
abstract class AbstractStanbolService implements StanbolService
{
    /**
     * <p>HTTP Accept Header</p>
     */
    const ACCEPT_HEADER = 'Accept';
    
    /**
     * <p>HTTP Content-Type Header</p>
     */
    const CONTENT_TYPE_HEADER = 'Content-Type';
    
    /**
     * <p>The Client instance</p>
     * @var Guzzle\Http\Client
     */
    protected $httpClient;
    
    /**
     * <p>Creates a new instance of the Service with the current Http client</p>
     * 
     * @param mixed $httpClient The Http client used to make the requests
     *
     */
    public function __construct($httpClient){
        //assert('$client != null', 'Stanbol http client is not null');
        $this->httpClient = $httpClient;
    }
    
    /**
     * <p>Gets the Http Client used by the service to perform requests</p>
     */
    public function getHttpClient() {
        return $this->httpClient;
    }
    
    /**
     * <p>Sets the Http Client used by the service to perform requests</p>
     * 
     * @param mixed $httpClient The Http Client instance
     */
    public function setHttpClient($httpClient) {
        $this->httpClient = $httpClient;
    }
    
    /**
     * <p>Executes the given request and returns the response 
     * if everything went well or throws an exception if an error occured during the request</p>
     * 
     * @param \Guzzle\Http\Message\RequestInterface the request to be executed
     * @return \Guzzle\Http\Message\Response the response of the request
     * @throws \Stanbol\Exception\StanbolClientHttpException
     */
    protected function executeRequest(\Guzzle\Http\Message\RequestInterface $request)
    {
        try {
            $response = $request->send();

            $statusCode = $response->getStatusCode();
            if ($response->isError()) {
                throw new StanbolServiceException("[HTTP " . $statusCodes . "] Error retrieving content from stanbol server");
            }
            return $response;
        } catch (\Exception $exception) {
            throw new StanbolServiceException($exception->getMessage());
        }
    }
    
}

?>
