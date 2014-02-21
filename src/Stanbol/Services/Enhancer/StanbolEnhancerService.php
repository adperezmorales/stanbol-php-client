<?php

namespace Stanbol\Services\Enhancer;

/**
 * <p>Stanbol Enhancer Service</p>
 * <p>Allows to enhance content extracting text annotations, entity annotations and entities</p>
 * 
 * @author Antonio David Perez Morales <adperezmorales@gmail.com>
 */
interface StanbolEnhancerService
{
    /**
     * <p>Contains the path of the Stanbol endpoint to access the enhancer service</p>
     */
    const ENHANCER_URL_PATH = "/enhancer";
    
    /**
     * <p>Contains the text plain content type</p>
     */
    const TEXT_PLAIN = 'text/plain; charset=UTF-8';
    
    
    /**
     * <p>Enhance the given content obtaining the enhancements of the content</p>
     * 
     * @param String $content The content to enhance
     * @return a <code>Enhancements</code> object containing the extracted enhancements
     * 
     * @throws \Stanbol\Services\Exception\StanbolServiceException
     */
    public function enhance($content);
    
    /**
     * <p>Selects the enhancement chain to be used in the enhancement process (fluent API)</p>
     * 
     * @param String $chain The chain to be used
     * @return The current StanbolEnhancerService instance
     */
    public function selectChain($chain);
    
    /**
     * <p>Selects the default chain for the enhancement process (fluent API)</p>
     * 
     * @return The current StanbolEnhancerService instance
     */
    public function selectDefaultChain();
    
    /**
     * <p>Gets the current configured chain to be used</p>
     */
    public function getChain();
    
}

?>
