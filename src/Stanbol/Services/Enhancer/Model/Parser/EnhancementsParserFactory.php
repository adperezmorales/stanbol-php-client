<?php

namespace Stanbol\Services\Enhancer\Model\Parser;

use Stanbol\Services\Enhancer\Model\Parser\EasyRdfEnhancementsParser;

/**
 * <p>EnhancementsParserFactory class</p>
 * <p>Factory to create EnhancementsParser instances</p>
 * 
 * @see \Stanbol\Services\Enhancer\Model\Parser\EnhancementsParser
 * @see \Stanbol\Services\Enhancer\Model\Parser\EasyRdfEnhancementsParser
 *
 * @author Antonio David Pérez Morales <adperezmorales@gmail.com>
 */
class EnhancementsParserFactory
{

    /**
     * <p>Create a default parser for the <code>EnhancementsParser</code></p>
     * <p>By default, the \Stanbol\Services\Enhancer\Model\Parser\EasyRdfEnhancementsParser class is used</p>
     * 
     * @param String $model The graph model as String to be parsed 
     * 
     * @return an instance of the \Stanbol\Services\Enhancer\Model\Parser\EnhancementsParser
     * 
     */
    public static final function createDefaultParser($model)
    {
        return new EasyRdfEnhancementsParser($model);
    }

}

?>
