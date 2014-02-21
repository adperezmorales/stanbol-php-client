<?php

namespace Stanbol\Services\Entityhub\Model;

use Stanbol\Client\Exception\StanbolClientException;

/**
 * <p>Represents an LDPath Program. The purpose of this class is to try to have a friendly way to programatically build LDPath programs in order
 * to ease the integration of the Stanbol Client in other applications or frameworks. Currently, it supports the definitions of Namespaces
 * and fields and the parsing of LDPath programs in String formats</p>
 *
 * @author Antonio David Perez Morales <adperezmorales@gmail.com>
 *
 */
class LDPathProgram
{
    /* Static RegExp Parsers */

    private static $prefixPattern = "/@prefix\\s*(\\S*)(?:(?:\\s*:\\s*)|(?::\\s*))<(\\S*)>(?:\\s*)?;/";
    private static $fieldPattern = "/(\\S*)\\s*=((?:[^;]*));/";

    /**
     * <p>Map of Prefix - Namespace</p>
     * @var array string key and string value
     */
    private $namespaces;

    /**
     * <p>Map of LDPathField - Field Definition</p>
     * @var array LDPathField key and string value
     */
    private $fields;

    /**
     * <p>Constructor</p>
     * <p>Creates a new instance of LDPathProgram</p>
     * 
     * <p>If a LDPath program in string format is given then it tries to parse and validate it</p>
     * 
     * @param string $ldpathProgram Optional. The LDPath program to be created
     */
    public function __construct($ldpathProgram = null)
    {
        $this->namespaces = array();
        $this->fields = array();

        if ($ldpathProgram != null && !empty($ldpathProgram)) {

            //Prefix Parsing
            preg_match_all(self::$prefixPattern, $ldpathProgram, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                if ($match[1] == null)
                    throw new StanbolClientException('LDPath Program Syntax Error. Prefix Definition Error');
                if ($match[2] == null)
                    throw new StanbolClientException('LDPath Program Syntax Error. Namespace Definition Error');

                $this->addNamespace($match[1], $match[2]);
            }

            $restProgram = $ldpathProgram;
            if (count($this->namespaces) > 0) {
                $lastPrefixOcurrence = strrpos($restProgram, '@prefix');
                $restProgram = substr($restProgram, $lastPrefixOcurrence);
                $restProgram = substr($restProgram, strpos($restProgram, ';') + 1);
            }

            //Parameter Parsing;
            preg_match_all(self::$fieldPattern, $ldpathProgram, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $fieldName = $match[1];
                if($fieldName == null)
                    throw new StanbolClientException('LDPath Program Sintax Error. Field Name Definition Error');
                
                $fieldDefinition = $match[2];
                if($fieldDefinition == null)
                    throw new StanbolClientException('LDPath Program Sintax Error. Field Definition Error');

                $colonIndex = strpos($fieldName, ':');
                if($colonIndex !== false) {
                    $fieldPrefix = substr($fieldName, 0, $colonIndex);
                    if(!isset($this->namespaces[$fieldPrefix]))
                            throw new StanbolClientException('LDPath Program Sintax Error. Field Name Prefix ['.$fieldPrefix.'] does not exist as Namespace Prefix');
                    $fieldValue = substr($fieldName, $colonIndex+1);
                    $this->addFieldDefinition($fieldValue, $fieldDefinition, $fieldPrefix);
                }
                else {
                    $this->addFieldDefinition($fieldName, $fieldDefinition);
                }
                
            }
        }
    }

    /**
     * <p>Adds a new namespace definition to the LDPath program</p>
     *
     * @param string $prefix Namespace Prefix
     * @param string $namespace Namespace URI
     */
    public function addNamespace($prefix, $namespace)
    {
        if (!in_array($namespace, $this->namespaces))
            $this->namespaces[$prefix] = $namespace;
    }

    /**
     * <p>Adds a new Field Definition to the LDPath Program</p>
     *
     * @param string $fieldName Field Name without prefix
     * @param string $fieldDefinition Field Definition
     * @param string $prefix Optional. Field Prefix
     * 
     * @throws StanbolClientException If the prefix is given and it does not exist as namespace prefix
     */
    public function addFieldDefinition($fieldName, $fieldDefinition, $prefix = null)
    {
        if ($prefix != null && !empty($prefix))
            if (!isset($this->namespaces[$prefix]))
                throw new StanbolClientException('LDPath Program Sintax Error. Field Name Prefix does not exist as Namespace Prefix');

        $key = is_null($prefix) ? $fieldName : $prefix.':'.$fieldName;
        $this->fields[$key] = trim($fieldDefinition);
    }

    /**
     * <p>Gets namespace definition by its prefix</p>
     *
     * @param string $prefix Namespace's Prefix
     * @return string Namespace Definition or null if the prefix does not exist
     */
    public function getNamespace($prefix)
    {
        return isset($this->namespaces[$prefix]) ? $this->namespaces[$prefix] : null;
    }

    /**
     * <p>Gets FieldDefinition Definition by its name and prefix (optionally)</p>
     *
     * @param string $fieldName Field Name
     * @param string $prefix Field prefix
     * @return string Field Definition
     */
    public function getFieldDefinition($fieldName, $prefix = null)
    {
        $key = is_null($prefix) ? $fieldName : $prefix.':'.$fieldName;
        return $this->fields[$key];
    }

    /**
     * <p>Gets the namespace prefix by its associated namespace definition
     *
     * @param string $namespace Namespace's URI
     * @return string Namespace's Prefix
     */
    public function getPrefix($namespace)
    {
        foreach ($this->namespaces as $prefix => $ns) {
            if ($namespace === $ns)
                return $prefix;
        }
        return null;
    }

    /**
     * <p>LDPath Program String representation</p>
     */
    public function __toString()
    {

        $result = '';

        // Prefixes
        foreach ($this->namespaces as $prefix => $ns) { //@prefix geo : <http://www.w3.org/2003/01/geo/wgs84_pos#> ;
            $result .= '@prefix ' . $prefix . ' : <' . $ns . '> ;'.PHP_EOL;
        }

        // Fields
        foreach ($this->fields as $ldpathField => $fieldDefinition) {
            $result .= $ldpathField . ' = ' . $fieldDefinition;
            if (!(substr($fieldDefinition, -1) == ';'))
                $result .= ';';
            $result .= PHP_EOL;
        }

        return rtrim($result);
    }

}

?>
