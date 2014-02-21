<?php

namespace Stanbol\Vocabulary\Model\Serialiser;

use Stanbol\Vocabulary\Model\Entity;
use Stanbol\Vocabulary\Model\Serialiser\EntitySerialiser;

/**
 * <p>RdfEntitySerialiser class</p>
 * <p>Serializes entities to RDF/XML format
 *
 * @author Antonio David Perez Morales <adperezmorales@gmail.com>
 */
class RdfXmlEntitySerialiser implements EntitySerialiser
{
    private static $DUMMY_RDF_TYPE_VALUE = 'http://dummyValue';
    
    /**
     * <p>Default constructor</p>
     */
    public function __construct()
    {
        
    }

    /**
     * <p>Serializes the entity to RDF/XML representation</p>
     * 
     * @param Entity $entity The entity to be serialized
     * 
     * @return The string representation in RDF/XML format
     */
    public function serialize(Entity $entity)
    {
        $model = new \EasyRdf_Graph($entity->getUri());
        $resource = $model->resource($entity->getUri());

        /* It is a bug in EasyRdf_Serialiser_RdfXml which removes the first rdf:type property value */
        $resource->addResource('rdf:type', self::$DUMMY_RDF_TYPE_VALUE);
        $entityProperties = $entity->getRawProperties();
        foreach ($entityProperties as $propertyName => $propertyArrayValues) {
            foreach ($propertyArrayValues as $key => $values) {
                $lang = null;
                /* Sets the lang if the key is a lang key */
                if ($key != 'value')
                    $lang = $key;
                foreach ($values as $value) {
                    if (filter_var($value, FILTER_VALIDATE_URL))
                        $resource->addResource($propertyName, $value);
                    else
                        $resource->addLiteral($propertyName, $value, $lang);
                }
            }
        }

        $serialiser = new \EasyRdf_Serialiser_RdfXml();
        return $serialiser->serialise($model, 'rdfxml');
    }

}

?>
