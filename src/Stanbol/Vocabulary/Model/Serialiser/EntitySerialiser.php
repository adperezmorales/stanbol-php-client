<?php

namespace Stanbol\Vocabulary\Model\Serialiser;

use Stanbol\Vocabulary\Model\Entity;

/**
 * <p>EntitySerialiser interface</p>
 * <p>Contract to serialize an entity to string</p>
 *
 * @author Antonio David Perez Morales <adperezmorales@gmail.com>
 */
interface EntitySerialiser
{
    /**
     * <p>Serializes the given Entity to String using the method provided by the serialiser</p>
     * 
     * @param Entity $entity The entity to be serialized
     * 
     * @return string The representation of the Entity
     */
    public function serialize(Entity $entity);
}

?>
