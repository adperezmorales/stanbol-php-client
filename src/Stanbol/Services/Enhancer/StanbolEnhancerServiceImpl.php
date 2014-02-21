<?php

namespace Stanbol\Services\Enhancer;

use Stanbol\Http\MediaType;
use Stanbol\Services\AbstractStanbolService;
use Stanbol\Services\Enhancer\StanbolEnhancerService;
use Stanbol\Services\Enhancer\Model\Parser\EnhancementsParserFactory;
use Stanbol\Services\Exception\StanbolServiceException;

/**
 * <p>Class representing the implementation of <code>StanbolEnhancerService</code></p>
 *
 * @see Stanbol\Services\StanbolEnhancerService
 * 
 * @author Antonio David Perez Morales <adperezmorales@gmail.com>
 */
class StanbolEnhancerServiceImpl extends AbstractStanbolService implements StanbolEnhancerService
{
    /**
     * <p>Default Chain Name</p>
     * @var string
     */
    private static $DEFAULT_CHAIN = 'default';

    /**
     * <p>The Stanbol Enhancer chain to use for enhancing</p>
     * @var string 
     */
    private $chain;

    /**
     * {@inheritdoc}
     */
    public function enhance($content)
    {
        $enhancerEndpoint = $this->buildEnhancerPath();

        /*
         * The httpClient already contains the Stanbol Endpoint, so it is only needed to add the Enhancer part
         */
        $request = $this->httpClient->post($enhancerEndpoint, array(self::CONTENT_TYPE_HEADER => MediaType::TEXT_PLAIN, self::ACCEPT_HEADER => MediaType::APPLICATION_RDF_XML), $content);
        $response = $this->executeRequest($request);

        $enhancementsParser = EnhancementsParserFactory::createDefaultParser($response->getBody(true));
        $enhancements = $enhancementsParser->createEnhancements();

        return $enhancements;
    }

    /**
     * {@inheritdoc}
     */
    public function selectChain($chain)
    {
        if (!empty($chain) && $chain != self::$DEFAULT_CHAIN)
            $this->chain = $chain;
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function selectDefaultChain()
    {
        $this->chain = '';
        return $this;
    }

    /**
     * <p>Gets the current configured chain to be used</p>
     * 
     * @return The string containing the current configured chain
     */
    public function getChain()
    {
        if (empty($this->chain))
            return self::$DEFAULT_CHAIN;
        return $this->chain;
    }

    /**
     * <p>Constructs the path for the enhancer to be called using the client, using the enhancer path and selected chain (if selected)</p>
     */
    private function buildEnhancerPath()
    {
        $enhancerEndpoint = self::ENHANCER_URL_PATH;
        if (!empty($this->chain))
            $enhancerEndpoint .= 'chain/' . $this->chain;

        return $enhancerEndpoint;
    }

}

?>
