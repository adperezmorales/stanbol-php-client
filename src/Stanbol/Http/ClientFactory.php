<?php

namespace Stanbol\Http;

use Guzzle\Common\Event;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;

/**
 * <p>Client Factory</p>
 * <p>Singleton</p>
 * 
 * @author Antonio David Perez Morales <adperezmorales@gmail.com>
 */
class ClientFactory
{

    /**
     * <p>The unique ClientFactory instance</p>
     * @var ClientFactory
     */
    private static $instance;

    /**
     * <p>Private constructor for Singleton</p>
     */
    private function __construct()
    {
        
    }

    /**
     * <p>Gets the instance</p>
     * 
     * @return Stanbol\Http\Clientfactory instance
     */
    public static function getInstance()
    {
        if (self::$instance == null)
            self::$instance = new self();
        return self::$instance;
    }

    /**
     * {@inheritdoc}
     */
    public function create($baseUrl = '', $config = null)
    {
        $configuration = $config == null ? array() : (array) $config;
        $client = new Client($baseUrl, $configuration);
        /*
         * Allows the client to handle the HTTP error codes
         */
        $client->getEventDispatcher()->addListener('request.error', function(Event $event) {
                    if ($event['response']->isError()) {
                        $newResponse = new Response($event['response']->getStatusCode());
                        $event['response'] = $newResponse;
                        $event->stopPropagation();
                    }
                });
                return $client;
    }

}

?>
