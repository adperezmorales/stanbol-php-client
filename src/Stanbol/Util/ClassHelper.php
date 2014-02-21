<?php

namespace Stanbol\Util;

/**
 * <p>Helper Class to deal with Object classes</p>
 *
 * @author Antonio David Pérez Morales <adperezmorales@gmail.com>
 */
class ClassHelper
{

    /**
     * <p>Gets the name of the class of the given var removing the namespace if exists</p>
     * <p>Example:
     *      <ul>
     *          <li>Test_Class => Test_Class</li>
     *          <li>My\Package\Test_Class => Test_Class</li>
     *      </ul>
     * </p>
     * 
     * @param string|object $var The name of the class (namespaced or not) or the object to obtain the class name
     */
    public static function getClassName($var)
    {
        $class = is_object($var) ? get_class($var) : (string) $var;

        // work around for PHP 5.3.0 - 5.3.2 https://bugs.php.net/50731
        if ('\\' == $class[0]) {
            $class = substr($class, 1);
        }

        $pos = strrpos($class, '\\');

        $className = $pos > 0 ? substr($class, $pos + 1) : $class;

        return $className;
    }

    /**
     * <p>Cast an object into another one</p>
     * @param string|object $destination the class name or object to be the result of the casting
     * @param object $sourceObject The object to cast
     * @return object The casted object 
     */
    public static function cast($destination, $sourceObject)
    {
        if (is_string($destination)) {
            $destination = new $destination();
        }
        $sourceReflection = new ReflectionObject($sourceObject);
        $destinationReflection = new ReflectionObject($destination);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);
            $name = $sourceProperty->getName();
            $value = $sourceProperty->getValue($sourceObject);
            if ($destinationReflection->hasProperty($name)) {
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);
                $propDest->setValue($destination, $value);
            } else {
                $destination->$name = $value;
            }
        }
        return $destination;
    }

}

?>
