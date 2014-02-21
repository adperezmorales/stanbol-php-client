<?php

namespace Stanbol\Tests\Http;

use Stanbol\Tests\StanbolBaseTestCase;
use Stanbol\Http\ClientFactory;

/**
 * <p>Client Factory Tests</p>
 * 
 * @author Antonio David Perez Morales <adperezmorales@gmail.com>
 */
class ClientFactoryTest extends StanbolBaseTestCase
{
    
    private static $clientFactory;
    
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$clientFactory = ClientFactory::getInstance();
    }
    
    /**
     * @test
     * @covers \Stanbol\Util\Http\ClientFactort::create
     */
    public function testCcreate() {
        $client = self::$clientFactory->create("http://localhost:8080");
        $this->assertNotNull($client);
        $this->assertInstanceOf('\Guzzle\Http\ClientInterface', $client);
        $this->assertCount(1, $client->getConfig());
    }


}

?>
