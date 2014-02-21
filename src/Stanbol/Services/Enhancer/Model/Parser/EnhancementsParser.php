<?php

namespace Stanbol\Services\Enhancer\Model\Parser;
/**
 * <p>Interface to deal with the Enhancements graph model</p>
 *
 * @author Antonio David Pérez Morales <adperezmorales@gmail.com>
 */
interface EnhancementsParser
{

    /**
     * <p>Creates the \Stanbol\Services\Enhancer\Model\Enhancements object from the given model</p>
     * 
     * @return object A \Stanbol\Services\Enhancer\Model\Enhancements object instance representing the enhancements
     */
    public function createEnhancements();

    /**
     * <p>Parses the enhancements contained in the given model</p>
     * <p>Returns both TextAnnotation and EntityAnnotation enhancements</p>
     *
     * @return array An array containing the \Stanbol\Services\Enhancer\Model\Enhancement objects
     */
    public function parseEnhancements();
    
    /**
     * <p>Parses the languages of the enhancements</p>
     * 
     * @return array An array containing the languages as string
     */
    public function parseLanguages();
    
    /**
     * <p>Parse the TextAnnotations contained in the model into <code>\Stanbol\Services\Enhancer\Model\TextAnnotation</code> objects</p>
     * 
     * @return array An array containing the \Stanbol\Services\Enhancer\Model\TextAnnotation objects
     */
    public function parseTextAnnotations();
    
    /**
     * <p>Parse the EntityAnnotations contained in the model into <code>\Stanbol\Services\Enhancer\Model\EntityAnnotation</code> objects
     * 
     * @return array An array containing the \Stanbol\Services\Enhancer\Model\EntityAnnotation objects
     */
    public function parseEntityAnnotations();
    
    /**
     * <p>Parse an entity identified by the entity uri into \Stanbol\Services\Vocabulary\Model\Entity</p>
     * 
     * @param type $entityUri the entity uri to parse
     * 
     * @return \Stanbol\Services\Vocabulary\Model\Entity the parsed entity
     */
    public function parseEntity($entityUri);
}

?>
