<?php

namespace Stanbol\Services\Enhancer\Model\Parser;

use Stanbol\Services\Enhancer\Model\Parser\EnhancementsParser;
use Stanbol\Services\Enhancer\Model\Enhancements;
use Stanbol\Services\Enhancer\Model\Enhancement;
use Stanbol\Services\Enhancer\Model\TextAnnotation;
use Stanbol\Services\Enhancer\Model\EntityAnnotation;
use Stanbol\Ontology\DCTerms;
use Stanbol\Ontology\EntityHub;
use Stanbol\Ontology\FISE;
use Stanbol\Ontology\RDF;
use Stanbol\Vocabulary\Model\Entity;

/**
 * <p>Class representing an EnhancementsParser</p>
 * <p>It uses EasyRdf library in order to parse the Enhancements</p>
 *
 * @author Antonio David Pérez Morales <adperezmorales@gmail.com>
 * 
 */
class EasyRdfEnhancementsParser implements EnhancementsParser
{

    /**
     * <p>EasyRdf_Graph containing the enhancements model</p>
     * @var EasyRdf_Graph
     */
    private $model;

    /**
     * <p>Constructor</p>
     * <p>Constructs an instance using the enhancements passed as string parameter</p>
     * 
     * @param string $rawModel the string containing the enhancements model (in RDF)
     */
    public function __construct($rawModel)
    {
        $this->model = new \EasyRdf_Graph();
        $this->model->parse($rawModel);
    }

    /**
     * <p>Constant containing the value of the Language Detection Enhancement Engine used by Stanbol</p>
     * @var string
     */
    private static $LANGUAGE_DETECTION_ENHANCEMENT_ENGINE = "org.apache.stanbol.enhancer.engines.langdetect.LanguageDetectionEnhancementEngine";

    /**
     * <p>Creates the \Stanbol\Services\Enhancer\Model\Enhancements object from the given model</p>
     * 
     * @return object A \Stanbol\Services\Enhancer\Model\Enhancements object instance representing the enhancements 
     */
    public function createEnhancements()
    {
        $enhancements = new Enhancements($this->model);
        $enhancements->setEnhancements($this->parseEnhancements($this->model));
        $enhancements->setLanguages($this->parseLanguages($this->model));
        return $enhancements;
    }

    /**
     * <p>Parses the enhancements contained in the model</p>
     * <p>Returns both TextAnnotation and EntityAnnotation enhancements</p>
     * 
     * @return array An array containing the \Stanbol\Services\Enhancer\Model\Enhancement objects
     */
    public function parseEnhancements()
    {

        $result = $this->parseTextAnnotations($this->model);
        $result = array_merge($result, $this->parseEntityAnnotations($this->model));

        $modelArray = $this->model->toArray();
        foreach ($result as $enhancementUri => $enhancementInstance) {
            $enhancementProperties = $modelArray[$enhancementUri];
            if (isset($enhancementProperties[DCTerms::RELATION])) {
                $relations = array();
                foreach ($enhancementProperties[DCTerms::RELATION] as $relationTypedValue) {
                    if (isset($result[$relationTypedValue['value']]))
                        $relations[$relationTypedValue['value']] = $result[$relationTypedValue['value']];
                }

                $enhancementInstance->setRelations($relations);
            }
        }

        return $result;
    }

    /**
     * <p>Parse the TextAnnotations contained in the model into <code>\Stanbol\Services\Model\TextAnnotation</code> objects</p>
     * 
     * @return array An array containing the \Stanbol\Services\Enhancer\Model\TextAnnotation objects
     */
    public function parseTextAnnotations()
    {
        $enhancements = array();

        /*
         * Gets the text annotations
         */
        $textAnnotations = $this->model->resourcesMatching(RDF::TYPE, array('type' => 'uri', 'value' => FISE::TEXT_ANNOTATION));
        $modelArray = $this->model->toArray();

        foreach ($textAnnotations as $taResource) {
            if (isset($modelArray[$taResource->getUri()][DCTerms::CREATOR]) && $modelArray[$taResource->getUri()][DCTerms::CREATOR][0]['value'] == self::$LANGUAGE_DETECTION_ENHANCEMENT_ENGINE)
                continue;

            $textAnnotation = new TextAnnotation($taResource->getUri());
            $this->setTextAnnotationData($textAnnotation);

            $enhancements[$textAnnotation->getUri()] = $textAnnotation;
        }

        return $enhancements;
    }

    /**
     * <p>Parse the EntityAnnotations contained in the model into <code>\Stanbol\Services\Enhancer\Model\EntityAnnotation</code> objects
     * 
     * @return array An array containing the \Stanbol\Services\Enhancer\Model\EntityAnnotation objects
     */
    public function parseEntityAnnotations()
    {
        $enhancements = array();

        /*
         * Gets the entity annotations
         */
        $entityAnnotations = $this->model->resourcesMatching(RDF::TYPE, array('type' => 'uri', 'value' => FISE::ENTITY_ANNOTATION));
        foreach ($entityAnnotations as $eaResource) {

            $entityAnnotation = new EntityAnnotation($eaResource->getUri());
            $this->setEntityAnnotationData($entityAnnotation);

            $enhancements[$entityAnnotation->getUri()] = $entityAnnotation;
        }

        return $enhancements;
    }

    /**
     * <p>Parse the languages of the enhancements</p>
     * 
     * 
     * @return array An array containing the languages
     */
    public function parseLanguages()
    {
        $languages = array();
        $modelArray = $this->model->toArray();
        $textAnnotationsLanguage = $this->model->resourcesMatching(DCTerms::TYPE, array('type' => 'uri', 'value' => DCTerms::LINGUISTIC_SYSTEM));
        foreach ($textAnnotationsLanguage as $taLangResource) {
            $taProperties = $modelArray[$taLangResource->getUri()];
            $lang = isset($taProperties[DCTerms::LANGUAGE][0]['value']) ? $taProperties[DCTerms::LANGUAGE][0]['value'] : '';
            if (!empty($lang) && !in_array($lang, $languages))
                array_push($languages, $lang);
        }

        return $languages;
    }

    /**
     * <p>Sets the data related to a TextAnnotation</p>
     * 
     * @param \Stanbol\Services\Enhancer\Model\TextAnnotation $textAnnotation The <code>\Stanbol\Services\Enhancer\Model\TextAnnotation</code> to be populated
     */
    private function setTextAnnotationData(TextAnnotation $textAnnotation)
    {
        /*
         * Sets the common data for an enhancement 
         */
        $this->setEnhancementData($textAnnotation);

        /*
         * Convert the model to an array for ease of manipulation
         */
        $modelArray = $this->model->toArray();
        $taProperties = $modelArray[$textAnnotation->getUri()];

        $textAnnotation->setType(isset($taProperties[DCTerms::TYPE][0]['value']) ? (string) $taProperties[DCTerms::TYPE][0]['value'] : null);
        $textAnnotation->setStarts(isset($taProperties[FISE::START][0]['value']) ? intval($taProperties[FISE::START][0]['value']) : 0);
        $textAnnotation->setEnds(isset($taProperties[FISE::END][0]['value']) ? intval($taProperties[FISE::END][0]['value']) : 0);
        $textAnnotation->setSelectedText(isset($taProperties[FISE::SELECTED_TEXT][0]['value']) ? (string) $taProperties[FISE::SELECTED_TEXT][0]['value'] : null);
        $textAnnotation->setSelectionContext(isset($taProperties[FISE::SELECTION_CONTEXT][0]['value']) ? (string) $taProperties[FISE::SELECTION_CONTEXT][0]['value'] : null);
        $textAnnotation->setLanguage(isset($taProperties[DCTerms::LANGUAGE][0]['value']) ? (string) $taProperties[DCTerms::LANGUAGE][0]['value'] : null);

        if (!isset($taProperties[DCTerms::LANGUAGE]) && isset($taProperties[FISE::SELECTED_TEXT][0]['lang']))
            $textAnnotation->setLanguage($taProperties[FISE::SELECTED_TEXT][0]['lang']);
    }

    /**
     * <p>Sets the data related to a EntityAnnotation</p>
     * 
     * @param \Stanbol\Services\Enhancer\Model\EntityAnnotation $textAnnotation The <code>\Stanbol\Services\Enhancer\Model\EntityAnnotation</code> to be populated
     */
    private function setEntityAnnotationData(EntityAnnotation $entityAnnotation)
    {
        /*
         * Sets the common data for an enhancement 
         */
        $this->setEnhancementData($entityAnnotation);

        /*
         * Convert the model to an array for ease of manipulation
         */
        $modelArray = $this->model->toArray();
        $eaProperties = $modelArray[$entityAnnotation->getUri()];

        $entityAnnotation->setEntityLabel(isset($eaProperties[FISE::ENTITY_LABEL][0]['value']) ? (string) $eaProperties[FISE::ENTITY_LABEL][0]['value'] : null);
        $entityAnnotation->setEntityReference(isset($eaProperties[FISE::ENTITY_LABEL][0]['value']) ? (string) $eaProperties[FISE::ENTITY_LABEL][0]['value'] : null);

        if (isset($eaProperties[FISE::ENTITY_TYPE])) {
            $entityTypes = array();
            foreach ($eaProperties[FISE::ENTITY_TYPE] as $typedValue) {
                array_push($entityTypes, $typedValue['value']);
            }

            $entityAnnotation->setEntityTypes($entityTypes);
        }

        if (isset($eaProperties[FISE::ENTITY_REFERENCE])) {
            $entityReferenceUri = $eaProperties[FISE::ENTITY_REFERENCE][0]['value'];
            $entity = $this->parseEntity($entityReferenceUri);
            $entityAnnotation->setEntityReference($entity);
        }

        $entityAnnotation->setSite(isset($eaProperties[EntityHub::SITE][0]['value']) ? (string) $eaProperties[EntityHub::SITE][0]['value'] : null);
    }

    /**
     * <p>Sets the data related to an enhancement</p>
     * 
     * @param \Stanbol\Services\Enhancer\Model\Enhancement $enhancement The <code>\Stanbol\Services\Enhancer\Model\Enhancement</code> to be populated
     */
    private function setEnhancementData(Enhancement $enhancement)
    {
        /*
         * Convert the model to an array for ease of manipulation
         */
        $modelArray = $this->model->toArray();
        $enProperties = $modelArray[$enhancement->getUri()];

        $enhancement->setConfidence(isset($enProperties[FISE::CONFIDENCE][0]['value']) ? floatval($enProperties[FISE::CONFIDENCE][0]['value']) : 0);
        $enhancement->setCreated(isset($enProperties[DCTerms::CREATED][0]['value']) ? (string) ($enProperties[DCTerms::CREATED][0]['value']) : null);
        $enhancement->setCreator(isset($enProperties[DCTerms::CREATOR][0]['value']) ? (string) ($enProperties[DCTerms::CREATOR][0]['value']) : null);
        $enhancement->setLanguage(isset($enProperties[DCTerms::LANGUAGE][0]['value']) ? (string) ($enProperties[DCTerms::LANGUAGE][0]['value']) : null);
        $enhancement->setExtractedFrom(isset($enProperties[FISE::EXTRACTED_FROM][0]['value']) ? (string) ($enProperties[FISE::EXTRACTED_FROM][0]['value']) : null);
    }

    /**
     * <p>Parse an entity identified by the entity uri into \Stanbol\Vocabulary\Model\Entity</p>
     * 
     * @param type $entityUri the entity uri to parse
     * 
     * @return \Stanbol\Vocabulary\Model\Entity the parsed entity
     */
    public function parseEntity($entityUri)
    {
        $entity = new Entity($entityUri);
        $modelArray = $this->model->toArray();
        
        $entityProperties = $modelArray[$entityUri];
        foreach ($entityProperties as $property => $arrayValues) {
            foreach ($arrayValues as $propertyArrayValue) {
                /*
                 * $propertyArrayValue is in the form of:
                 *  array(
                 *      'type' => uri|literal,
                 *      'value' => THE_VALUE,
                 *      'lang' => LANGUAGE (optional),
                 *      'datatype' => DATATYPE (optional)
                 *       );
                 *
                 */
                $lang = isset($propertyArrayValue['lang']) ? $propertyArrayValue['lang'] : null;
                $entity->addPropertyValue($property, $propertyArrayValue['value'], $lang);
            }
        }

        return $entity;
    }

    /**
     * <p>Updates the languages of the enhancements getting
     * @param \Stanbol\Services\Enhancer\Model\Enhancements $enhancements The \Stanbol\Services\Enhancer\Model\Enhancements to update
     */
    private function updateEnhancementsLanguages(Enhancements $enhancements)
    {
        $modelArray = $this->model->toArray();
        $textAnnotationsLanguages = $this->model->resourcesMatching(DCTerms::CREATOR, array('type' => 'literal', 'value' => self::$LANGUAGE_DETECTION_ENHANCEMENT_ENGINE));
        foreach ($textAnnotationsLanguages as $talResource) {
            $talProperties = $modelArray[$talResource->getUri()];
            $existLanguage = isset($talProperties[DCTerms::LANGUAGE]);
            if ($existLanguage)
                $enhancements->addLanguage($talProperties[DCTerms::LANGUAGE][0]['value']);
        }
    }

}

?>
