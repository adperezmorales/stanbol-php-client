<?php

namespace Stanbol\Services\Enhancer\Model;

use Stanbol\Services\Enhancer\Model\Enhancement;

/**
 * <p>EntityAnnotation Model</p>
 * <p>Represents an EntityAnnotation in the Enhancement model</p>
 *
 * @author Antonio David Pérez Morales <adperezmorales@gmail.com>
 */
class EntityAnnotation extends Enhancement
{

    // properties
    private $entityLabel = null; // http://fise.iks-project.eu/ontology/entity-label

    /**
     * @var mixed RedLink\Vocabulary\Model\Entity 
     */
    private $entityReference = null; // http://fise.iks-project.eu/ontology/entity-reference
    private $entityTypes = null; // array http://fise.iks-project.eu/ontology/entity-type
    private $site = null; // http://stanbol.apache.org/ontology/entityhub/entityhub#"

    public function __construct($uri = '')
    {
        parent::__construct($uri);
    }

    public function getEntityLabel()
    {
        return $this->entityLabel;
    }

    public function setEntityLabel($entityLabel)
    {
        $this->entityLabel = $entityLabel;
    }

    /**
     * @return \RedLink\Vocabulary\Model\Entity instance 
     */
    public function getEntityReference()
    {
        return $this->entityReference;
    }

    /**
     * 
     * @param \Stanbol\Vocabulary\Model\Entity $entityReference The entity referenced by the EntityAnnotation
     */
    public function setEntityReference($entityReference)
    {
        $this->entityReference = $entityReference;
    }

    public function getEntityTypes()
    {
        return $this->entityTypes;
    }

    public function setEntityTypes($entityTypes)
    {
        $this->entityTypes = $entityTypes;
    }

    public function getSite()
    {
        return $this->site;
    }

    public function setSite($site)
    {
        $this->site = $site;
    }

}

?>
