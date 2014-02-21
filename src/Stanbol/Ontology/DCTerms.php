<?php

namespace Stanbol\Ontology;

/**
 * Vocabulary definitions from vocabularies/dublin-core_terms.xml 
 * @author Antonio David Pérez Morales <adperezmorales@gmail.com>
 */
class DCTerms
{
    /**
     * <p>The namespace of the vocabulary as a string ({@value})</p>
     */
    const NS = "http://purl.org/dc/terms/";

    /**
     * <p>Creator</p>
     */
    const CREATOR = "http://purl.org/dc/terms/creator";
    /**
     * <p>Created</p>
     */
    const CREATED = "http://purl.org/dc/terms/created";
    /**
     * <p>Language</p>
     */
    const LANGUAGE = "http://purl.org/dc/terms/language";
    
    /**
     * <p>Linguistic system</p>
     */
    const LINGUISTIC_SYSTEM = "http://purl.org/dc/terms/LinguisticSystem";
    /**
     * <p>Relation</p>
     */
    const RELATION = "http://purl.org/dc/terms/relation";
    /**
     * <p>Type</p>
     */
    const TYPE = "http://purl.org/dc/terms/type";
    
}

?>
