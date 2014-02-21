<?php

namespace Stanbol\Tests\Services\Entityhub\Model;

use Stanbol\Tests\StanbolBaseTestCase;
use Stanbol\Services\Entityhub\Model\LDPathProgram;

/**
 * <p>LDPathProgram Tests</p>
 *
 * @author Antonio David Perez Morales <adperezmorales@gmail.com>
 */
class LDPathProgramTest extends StanbolBaseTestCase
{
    private static $VALID_LDPATH_PROGRAM = '@prefix dct : <http://purl.org/dc/terms/> ;
@prefix geo : <http://www.w3.org/2003/01/geo/wgs84_pos#> ;
name = rdfs:label[@en] :: xsd:string;
labels = rdfs:label :: xsd:string;
comment = rdfs:comment[@en] :: xsd:string;
categories = dc:subject :: xsd:anyURI;
homepage = foaf:homepage :: xsd:anyURI;
location = fn:concat("[",geo:lat,",",geo:long,"]") :: xsd:string;';
    
    private static $INVALID_LDPATH_PROGRAM = '@prefix dct : <http://purl.org/dc/terms/> ;
@prefix geo : <http://www.w3.org/2003/01/geo/wgs84_pos#> ;
invalidprefix:name = rdfs:label[@en] :: xsd:string;
= rdfs:label :: xsd:string;
comment = rdfs:comment[@en] :: xsd:string;
categories = dc:subject :: xsd:anyURI;
homepage = foaf:homepage :: xsd:anyURI;
location = fn:concat("[",geo:lat,",",geo:long,"]") :: xsd:string;';
    
    private $ldpathProgram;
    
    protected function setUp()
    {
        parent::setUp();
        $this->ldpathProgram = new LDPathProgram();

    }

    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\Model\LDPathProgram::getNamespace
     * @covers \Stanbol\Services\Entityhub\Model\LDPathProgram::addNamespace
     */
    public function testNamespace() {
        $this->ldpathProgram->addNamespace('prefix', 'namespace');
        
        $result = $this->ldpathProgram->getNamespace('prefix');
        
        $this->assertEquals('namespace', $result);
        
        $notExisting = $this->ldpathProgram->getNamespace('not_exist');
        $this->assertEquals(null, $notExisting);
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\Model\LDPathProgram::getPrefix
     */
    public function testPrefix() {
        $this->ldpathProgram->addNamespace('prefix', 'namespace');
        $result = $this->ldpathProgram->getPrefix('namespace');
        
        $this->assertEquals('prefix', $result);
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\Model\LDPathProgram::addFieldDefinition
     * @covers \Stanbol\Services\Entityhub\Model\LDPathProgram::getFieldDefinition
     */
    public function testFieldDefinition() {
        $this->ldpathProgram->addNamespace('rdf', 'rdf_namespace');
        $this->ldpathProgram->addFieldDefinition('label', 'labelDefinition', 'rdf');
        $result = $this->ldpathProgram->getFieldDefinition('label', 'rdf');
        
        $this->assertEquals('labelDefinition', $result);
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\Model\LDPathProgram::addFieldDefinition
     * @expectedException \Stanbol\Client\Exception\StanbolClientException
     */
    public function testFieldDefinitionError() {
        /* Adding field definition of an unknown namespace */
        $this->ldpathProgram->addFieldDefinition('label', 'labelDefinition', 'not_existing_prefix');
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\Model\LDPathProgram::__construct
     */
    public function testLDPath() {
        $ldpathProgram = new LDPathProgram(self::$VALID_LDPATH_PROGRAM);
        
        $this->assertEquals('http://purl.org/dc/terms/', $ldpathProgram->getNamespace('dct'));
        $fieldDefinition = $ldpathProgram->getFieldDefinition('name');
        
        $this->assertEquals('rdfs:label[@en] :: xsd:string', $fieldDefinition);
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\Model\LDPathProgram::__construct
     * @expectedException \Stanbol\Client\Exception\StanbolClientException
     */
    public function testLDPathError() {
      $ldpathProgram = new LDPathProgram(self::$INVALID_LDPATH_PROGRAM);  
    }
}

?>
