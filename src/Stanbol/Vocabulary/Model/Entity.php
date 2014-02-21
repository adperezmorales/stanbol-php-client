<?php

namespace Stanbol\Vocabulary\Model;

/**
 * <p>Represents an Entity</p>
 * <p>An entity is identified by an URI and it contains a set of properties</p>
 *
 * @author Antonio David Pérez Morales
 */
class Entity
{

    /**
     * Entity URI
     */
    private $uri;

    /**
     * Properties' table -> <Property URI, Language, Property Value>
     */
    private $properties;
    
    /**
     * <p>Default Constructor</p>
     * 
     * @param String $uri The URI of the entity
     */
    public function __construct($uri = '')
    {
        $this->properties = array();
        $this->uri = $uri;
    }

    /**
     * <p>Gets the uri of the entity</p>
     * 
     * @return String the uri of the entity
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * <p>Sets the uri of the entity</p>
     * 
     * @param String $uri The uri of the entity
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * <p>Adds a property value</p>
     * <p>If language is specified, then a new property value for the given language is added</p>
     * 
     * @param String $property The property where to add the new value
     * @param String $value The value to be added
     * @param String $lang The language of the value (Optional)
     */
    public function addPropertyValue($property, $value, $lang = null)
    {
        $this->checkProperty($property, $lang);
        if ($lang != null)
            array_push($this->properties[$property][$lang], $value);
        else
            array_push($this->properties[$property]['value'], $value);
    }

    /**
     * <p>Sets a property value</p>
     * <p>If the property already exists, all the existing values will be removed before setting the new value</p>
     * 
     * @param String $property The property to be set
     * @param String $value The value to be added
     * @param String $lang The language of the value (Optional)
     */
    public function setProperty($property, $value, $lang = null) {
        if(isset($this->properties[$property]))
                unset($this->properties[$property]);
        $this->checkProperty($property, $lang);
        $this->addPropertyValue($property, $value, $lang);
    }
    
    /**
     * <p>Gets the values of the property</p>
     * 
     * @param String $property The property to obtain the values
     */
    public function getValues($property)
    {
        $result = array();
        foreach ($this->properties[$property] as $key => $values) {
            $result = array_merge($result, $values);
        }

        return array_unique($result);
    }

    /**
     * <p>Gets the value of a property as string</p>
     * <p>First try to obtain the value of the property for the given lang (if given).
     * If not exists, then it tries to return the value of the property without using the lang</p>
     * 
     * @param String $property The property
     * @param String $lang The language of the property value
     * 
     * @return The value of the property or null otherwise
     */
    public function getValue($property, $lang = null)
    {
        $propertyLang = null;

        if ($lang != null)
            $propertyLang = isset($this->properties[$property][$lang]) && count($this->properties[$property][$lang]) > 0 ? $this->properties[$property][$lang][0] : null;
        else if ($propertyLang == null)
            $propertyLang = isset($this->properties[$property]['value']) && count($this->properties[$property]['value']) > 0 ? $this->properties[$property]['value'][0] : null;

        return $propertyLang;
    }

    /**
     * <p>Gets the entity property names</p>
     * 
     * @return array An array containing the property names
     */
    public function getProperties()
    {
        return array_keys($this->properties);
    }

    /**
     * <p>Gets the internal array with all the properties and values (with the language if having)</p>
     * 
     * @return array An array containing the property names and their values
     */
    public function getRawProperties() {
        return array_merge(array(), $this->properties);
    }
    
    /**
     * <p>Gets the first value of a property if exists</p>
     * 
     * @param String $property The property
     * @return mixed The property value if exists or null otherwise
     */
    public function getFirstPropertyValue($property)
    {
        $values = $this->getValues($property);
        return isset($values[0]) ? $values[0] : null;
    }

    /**
     * <p>Checks a property in order to initialize it if necessary (using also the language if specified)</p>
     * 
     * @param String $property The property to check
     * @param String $lang The lang to check (Optional)
     */
    private function checkProperty($property, $lang = null)
    {
        if (!isset($this->properties[$property]))
            $this->properties[$property] = array('value' => array());

        if ($lang != null && !isset($this->properties[$property][$lang]))
            $this->properties[$property][$lang] = array();
    }
    
    

}

?>
