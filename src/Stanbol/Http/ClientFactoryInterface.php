<?php

namespace Stanbol\Http;

/**
 * <p>ClientFactoryInterface representing the contract to create Clients</p>
 *
 * @author Antonio David Perez Morales <adperezmorales@gmail.com>
 */
interface ClientFactoryInterface
{

    /**
     * Creates a HTTP client
     *
     * @param string $baseUrl A base URL of the web service
     * @param mixed $config Configuration settings
     *
     * @return The client to be used to perform Http requests (e.g \Guzzle\Http\ClientInterface)
     */
    public function create($baseUrl = '', $config = null);
}

?>
