<?php

namespace Stanbol\Services\Enhancer\Model;

/**
 * <p>Enhancement Model</p>
 * <p>Represents an enhancement in the Enhancement model</p>
 *
 * @author Antonio David Pérez Morales <adperezmorales@gmail.com>
 */
abstract class Enhancement
{
    /**
     * The uri of the enha
     * @var string
     */
    protected $uri;

    // properties
    
    /**
     * The created date
     * @var string
     */
    protected $created = null; // http://purl.org/dc/terms/created
    
    /**
     * The creator
     * @var string
     */
    protected $creator = null; // http://purl.org/dc/terms/creator
    
    /**
     * The language
     * @var string
     */
    protected $language = null; // http://purl.org/dc/terms/language
    
    /**
     * The confidence value
     * @var float
     * 
     */
    protected $confidence = null; // http://fise.iks-project.eu/ontology/confidence
    
    /**
     * The extracted-from value
     * @var string
     */
    protected $extractedFrom = null; // http://fise.iks-project.eu/ontology/extracted-from
    
    /**
     * The array of relations
     * @var array of \Stanbol\Services\Enhancer\Model\Enhancement
     * 
     */
    protected $relations = null; // http://purl.org/dc/terms/relation
    
    /**
     * <p>Default constructor</p>
     */
    public function __construct($uri = "") {
        $this->uri = $uri;
    }
    
   
    /**
     * <p>Gets the uri of the enhancement</p>
     * 
     * @return String the uri of the enhancement
     */
     public function getUri()
    {
        return $this->uri;
    }

    /**
     * <p>Sets the uri of the enhancement</p>
     * @param String $uri The uri to be set
     */
    public function setUri($uri)
    {
        $this->uri = uri;
    }

    /**
     * <p>Gets the created date</p>
     * @return String The created date
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * <p>Sets the created date</p>
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * <p>Gets the creator</p>
     * @return String The creator
     * 
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * <p>Sets the creator</p>
     * @param $creator The creator
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    /**
     * <p>Gets the language</p>
     * @return String The language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * <p>Sets the language</p>
     * @param String $language 
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * <p>Gets the confidence value</p>
     * @return float The confidene value
     */
    public function getConfidence()
    {
        return $this->confidence;
    }

    /**
     * <p>Sets the confidence value</p>
     * @param float $confidence 
     */
    public function setConfidence($confidence)
    {
        $this->confidence = $confidence;
    }

    /**
     * <p>Gets the extracted-from property</p>
     * @return String The extracted-from property value
     */
    public function getExtractedFrom()
    {
        return $this->extractedFrom;
    }

    /**
     * <p>Sets the extracted-from property</p>
     * @param String $extractedFrom The extracted-from value to be set
     */
    public function setExtractedFrom($extractedFrom)
    {
        $this->extractedFrom = $extractedFrom;
    }

    /**
     * <p>Gets the relations of the enhancement</p>
     * @return array The relations of the enhancement using the uri of each enhancement as array index and the \Stanbol\Services\Enhancer\Model\Enhancement instance as value
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * <p>Sets the relations of the enhancement</p>
     * @param array $relations The relations of the enhancement using the uri of each enhancement as array index and the \Stanbol\Services\Enhancer\Model\Enhancement instance as value
     */
    public function setRelations($relations)
    {
        $this->relations = $relations;
    }
}

?>
