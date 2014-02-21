<?php

namespace Stanbol\Tests\Services\Entityhub;

use Stanbol\Http\ClientFactory;
use Stanbol\Tests\StanbolBaseTestCase;
use Stanbol\Services\Entityhub\StanbolEntityhubService;
use Stanbol\Vocabulary\Model\Entity;

/**
 * <p>StanbolEntityhubServiceImplTest Tests</p>
 *
 * @author Antonio David Pérez Morales <adperezmorales@gmail.com>
 * @covers Stanbol\Services\Entityhub\StanbolEntityhubService
 */
class StanbolEntityhubServiceImplTest extends StanbolBaseTestCase
{

    private static $DUMMY_ENTITY_URI = 'http://example.org/dummyEntity';
    
    private $stanbolEntityhub;
    
    /**
     * <p>Mock Entityhub Service</p>
     * @var object
     */
    private $mockEntityhub;

    protected function setUp()
    {
        parent::setUp();
        $httpClient = ClientFactory::getInstance()->create(self::STANBOL_ENDPOINT);
        $this->stanbolEntityhub = new \Stanbol\Services\Entityhub\StanbolEntityhubServiceImpl($httpClient);
        $this->mockEntityhub = \Phockito::mock('\Stanbol\Services\Entityhub\StanbolEntityhubServiceImpl');
    }

    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::getSite
     */
    public function testGetSite() {
        $this->assertEmpty($this->stanbolEntityhub->getSite());
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::getSite
     */
    public function testSetSite() {
        $this->stanbolEntityhub->setSite('site');
        $this->assertEquals('site', $this->stanbolEntityhub->getSite());
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::getSite
     */
    public function testSetLocalSite() {
        $this->stanbolEntityhub->setSite('site');
        $this->assertEquals('site', $this->stanbolEntityhub->getSite());
        
        $this->stanbolEntityhub->setLocalSite();
        $this->assertEmpty($this->stanbolEntityhub->getSite());
    }
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::create
     */
    public function testCreate() {
        $entity = $this->createDummyEntity();
        \Phockito::when($this->mockEntityhub->create($entity))->return(self::$DUMMY_ENTITY_URI);
        $result = $this->mockEntityhub->create($entity);
        $this->assertEquals(self::$DUMMY_ENTITY_URI, $result);
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::create
     * @expectedException \InvalidArgumentException
     */
    public function testCreateBadParams() {
        $entity = new Entity();
        // Simulating null entity
        \Phockito::when($this->mockEntityhub->create($entity))->throw(new \InvalidArgumentException('Entity can not be null'));
        $this->mockEntityhub->create($entity);
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::createFromFile
     */
    public function testCreateFromFile() {
        \Phockito::when($this->mockEntityhub->createFromFile('file.rdf', self::$DUMMY_ENTITY_URI))->return(self::$DUMMY_ENTITY_URI);
        $result = $this->mockEntityhub->createFromFile('file.rdf', self::$DUMMY_ENTITY_URI);
        $this->assertEquals(self::$DUMMY_ENTITY_URI, $result);
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::create
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::createFromFile
     * @expectedException \Stanbol\Services\Exception\StanbolServiceException
     */
    public function testCreateExistingEntity() {
        $entity = $this->createDummyEntity();
        \Phockito::when($this->mockEntityhub->create($entity))->throw(new \Stanbol\Services\Exception\StanbolServiceException('Entity already exists'));
        $this->mockEntityhub->create($entity);
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::create
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::createFromFile
     */
    public function testCreateExistingEntityWithUpdateParam() {
        $entity = $this->createDummyEntity();
        \Phockito::when($this->mockEntityhub->create($entity, true))->return(self::$DUMMY_ENTITY_URI);
        $result = $this->mockEntityhub->create($entity, true);
        
        $this->assertEquals(self::$DUMMY_ENTITY_URI, $result);
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::get
     */
    public function testGet() {
        $entity = $this->createDummyEntity();
        \Phockito::when($this->mockEntityhub->get(self::$DUMMY_ENTITY_URI))->return($entity);
        $newEntity = $this->mockEntityhub->get(self::$DUMMY_ENTITY_URI);
        
        $this->assertEquals($entity, $newEntity);
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::get
     * @expectedException \InvalidArgumentException
     */
    public function testGetBadParams() {
        \Phockito::when($this->mockEntityhub->get(''))->throw(new \InvalidArgumentException('Id can not be null'));
        $newEntity = $this->mockEntityhub->get('');
        
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::create
     */
    public function testUpdate() {
        $entity = $this->createDummyEntity();
        \Phockito::when($this->mockEntityhub->update($entity))->return($entity);
        $result = $this->mockEntityhub->update($entity);
        $this->assertEquals($entity, $result);
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::create
     * @expectedException \InvalidArgumentException
     */
    public function testUpdateBadParams() {
        $entity = new Entity();
        // Simulating null entity
        \Phockito::when($this->mockEntityhub->update($entity))->throw(new \InvalidArgumentException('Entity can not be null'));
        $this->mockEntityhub->update($entity);
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::createFromFile
     */
    public function testUpdateFromFile() {
        $entity = $this->createDummyEntity();
        \Phockito::when($this->mockEntityhub->updateFromFile('file.rdf', self::$DUMMY_ENTITY_URI))->return($entity);
        $result = $this->mockEntityhub->updateFromFile('file.rdf', self::$DUMMY_ENTITY_URI);
        $this->assertEquals($entity, $result);
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::create
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::createFromFile
     * @expectedException \Stanbol\Services\Exception\StanbolServiceException
     */
    public function testUpdateNotExistingEntity() {
        $entity = $this->createDummyEntity();
        \Phockito::when($this->mockEntityhub->update($entity))->throw(new \Stanbol\Services\Exception\StanbolServiceException('Entity does not exist'));
        $this->mockEntityhub->update($entity);
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::create
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::createFromFile
     */
    public function testUpdateNotExistingEntityWithCreateParam() {
        $entity = $this->createDummyEntity();
        \Phockito::when($this->mockEntityhub->update($entity, true))->return($entity);
        $result = $this->mockEntityhub->update($entity, true);
        
        $this->assertEquals($entity, $result);
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::delete
     */
    public function testDelete() {
     \Phockito::when($this->mockEntityhub->delete(self::$DUMMY_ENTITY_URI))->return(true);
     $result = $this->mockEntityhub->delete(self::$DUMMY_ENTITY_URI);
             
     $this->assertTrue($result);
     
     // After deleting, the entity must not exist. Simulating it
     \Phockito::when($this->mockEntityhub->delete(self::$DUMMY_ENTITY_URI.'mock'))->return(false);
     $result = $this->mockEntityhub->delete(self::$DUMMY_ENTITY_URI.'mock');
             
     $this->assertFalse($result);
     
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::delete
     * @expectedException \InvalidArgumentException
     */
    public function testDeleteError() {
        \Phockito::when($this->mockEntityhub->delete(''))->throw(new \InvalidArgumentException('Id can not be null'));
        $this->mockEntityhub->delete('');
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::find
     */
    public function testFind() {
        $entity = $this->createDummyEntity();
        \Phockito::when($this->mockEntityhub->find('Dumm*'))->return($entity);
        
        $result = $this->mockEntityhub->find('Dumm*');
        $this->assertEquals($entity, $result);
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::find
     * @expectedException \InvalidArgumentException
     */
    public function testFindBadParams() {
        \Phockito::when($this->mockEntityhub->find(''))->throw(new \InvalidArgumentException('name can not be empty'));
        $this->mockEntityhub->find('');
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::find
     * @expectedException \Stanbol\Services\Exception\StanbolServiceException
     */
    public function testFindErrorExecution() {
        \Phockito::when($this->mockEntityhub->find('Test'))->throw(new \Stanbol\Services\Exception\StanbolServiceException('Error retrieving content'));
        $this->mockEntityhub->find('Test');
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::lookup
     */
    public function testLookup() {
        $entity = $this->createDummyEntity();
        \Phockito::when($this->mockEntityhub->lookup(self::$DUMMY_ENTITY_URI))->return($entity);
        
        $result = $this->mockEntityhub->lookup(self::$DUMMY_ENTITY_URI);
        
        $this->assertEquals($entity, $result);
        
        // Testing not existing entity
        \Phockito::when($this->mockEntityhub->lookup(self::$DUMMY_ENTITY_URI.'Mock'))->return(null);
        $result = $this->mockEntityhub->lookup(self::$DUMMY_ENTITY_URI.'Mock');
        $this->assertNull($result);
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::lookup
     * @expectedException \InvalidArgumentException
     */
    public function testLookupBadParams() {
        \Phockito::when($this->mockEntityhub->lookup(''))->throw(new \InvalidArgumentException('id can not be empty'));
        $this->mockEntityhub->lookup('');
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::lookup
     * @expectedException \Stanbol\Services\Exception\StanbolServiceException
     */
    public function testLookupErrorExecution() {
        \Phockito::when($this->mockEntityhub->lookup('Test'))->throw(new \Stanbol\Services\Exception\StanbolServiceException('Error retrieving content'));
        $this->mockEntityhub->lookup('Test');
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::ldpath
     */
    public function testLDPath() {
        $mockLDPathProgram = '@prefix dummy : <http://example.org/ontology/> ; name = rdfs:label[@en];
description = dummy:label;
categories = dc:subject :: xsd:anyURI;
url = foaf:homepage :: xsd:anyURI;
location = fn:concat("[",geo:lat,",",geo:long,"]") :: xsd:string;';
        
        \Phockito::when($this->mockEntityhub->ldpath(self::$DUMMY_ENTITY_URI, $mockLDPathProgram))->return(array(self::$DUMMY_ENTITY_URI => array()));
        
        $result = $this->mockEntityhub->ldpath(self::$DUMMY_ENTITY_URI, $mockLDPathProgram);
        
        $this->assertEquals(1, count(array_keys($result)));
        
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::ldpath
     * @expectedException \InvalidArgumentException
     */
    public function testLDPathBadParams() {
        \Phockito::when($this->mockEntityhub->ldpath(array(), ''))->throw(new \InvalidArgumentException('Context and LDPath can not be empty'));
        $this->mockEntityhub->ldpath(array(), '');
    }
    
    /**
     * @Test
     * @covers \Stanbol\Services\Entityhub\StanbolEntityhubService::ldpath
     * @expectedException \Stanbol\Services\Exception\StanbolServiceException
     */
    public function testLDPathErrorExecution() {
        \Phockito::when($this->mockEntityhub->ldpath(self::$DUMMY_ENTITY_URI, ''))->throw(new \Stanbol\Services\Exception\StanbolServiceException('Error retrieving content'));
        $this->mockEntityhub->ldpath(self::$DUMMY_ENTITY_URI, '');
    }
    
    private function createDummyEntity() {
        $entity = new Entity(self::$DUMMY_ENTITY_URI);
        $entity->addPropertyValue('http://example.org/ontology/label', 'DummyEntity');
        $entity->addPropertyValue('http://example.org/ontology/type', 'Place');
        return $entity;
    }
    
    /**
     * @expectedException \Stanbol\Services\Exception\StanbolServiceException
     */
    public function testFailedEnhancemenet()
    {
        $mockEnhancer = \Phockito::mock('\Stanbol\Services\Enhancer\StanbolEnhancerServiceImpl');
        \Phockito::when($mockEnhancer->enhance("Bad Text Simulating URL Not Found"))->throw(new \Stanbol\Services\Exception\StanbolServiceException("Enhancement Failed. Http Error Code 400"));
        
        $mockEnhancer->enhance('Bad Text Simulating URL Not Found');
    }

}

?>
