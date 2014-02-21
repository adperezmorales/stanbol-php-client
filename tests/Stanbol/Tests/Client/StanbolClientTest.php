<?php

namespace Stanbol\Tests\Client;

use Stanbol\Tests\StanbolBaseTestCase;
use Stanbol\Client\StanbolClient;
use Stanbol\Http\ClientFactory;

/**
 * <p>StanbolClientTest class</p>
 * <p>Tests for StanbolClient</p>
 *
 * @author Antonio David Perez Morales <adperezmorales@gmail.com>
 */
class StanbolClientTest extends StanbolBaseTestCase
{

    private $stanbolClient;

    /**
     * <p>Set Up</p>
     */
    protected function setUp()
    {
        parent::setUp();
        $this->stanbolClient = StanbolClient::getInstance(self::STANBOL_ENDPOINT);
    }

    /**
     * @covers \Stanbol\Client\StanbolClient::getHttpClient
     * @covers \Stanbol\Client\StanbolClient::setHttpClient
     */
    public function testHttpClient()
    {
        $httpClient = $this->stanbolClient->getHttpClient();
        $this->assertEquals(self::STANBOL_ENDPOINT, $httpClient->getBaseUrl());
        
        $client = ClientFactory::getInstance()->create(self::STANBOL_ENDPOINT, array());
        $this->stanbolClient->setHttpClient($client);
        $this->assertEquals(spl_object_hash($client), spl_object_hash($this->stanbolClient->getHttpClient()));
    }

    /**
     * @covers \Stanbol\Client\StanbolClient::setHttpClient
     * @covers \Stanbol\Client\StanbolClient::reloadHttpClientServices
     */
    public function testChangeHttpClient() {
        $this->stanbolClient->enhancer();
        $newConf = array('prop' => 'propValue', 'anotherProp' => 'anotherPropValue');
        
        $client = ClientFactory::getInstance()->create(self::STANBOL_ENDPOINT);
        $client->setConfig($newConf);
        $enhancerService = $this->stanbolClient->enhancer();
        $enhancerService->setHttpClient($client);
        
        $this->assertEquals(count($newConf), count($enhancerService->getHttpClient()->getConfig()->toArray()));
    }
    
    /**
     * @covers \Stanbol\Client\StanbolClient::getInstance
     */
    public function testStanbolClientSameInstance()
    {
        $stanbolClient = StanbolClient::getInstance(self::STANBOL_ENDPOINT);
        $this->assertEquals(spl_object_hash($stanbolClient), spl_object_hash($this->stanbolClient));
    }

}

?>
