<?php

namespace Stanbol\Tests\Services\Enhancer\Model;

use Stanbol\Services\Enhancer\Model\Parser\EnhancementsParser;
use Stanbol\Services\Enhancer\Model\Parser\EnhancementsParserFactory;

/**
 * <p>Enhancements Tests</p>
 *
 * @author Antonio David Pérez Morales <adperezmorales@gmail.com>
 * 
 * @covers \Stanbol\Services\Enhancer\Model\Enhancements
 */
class EnhancementsTest extends \PHPUnit_Framework_TestCase
{

    protected $enhancements;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->enhancements = null;
    }

    public function setUp()
    {
        parent::setUp();
        $this->enhancements = EnhancementsParserFactory::createDefaultParser(file_get_contents(STANBOL_TESTS_DIR . DIRECTORY_SEPARATOR . 'rdf.txt'))->createEnhancements();
    }

    /**
     * @covers \Stanbol\Services\Enhancer\Model\Enhancements::getLanguages
     */
    public function testGetLanguages()
    {

        $this->assertNotNull($this->enhancements->getLanguages());
        $this->assertCount(1, $this->enhancements->getLanguages());
    }

    /**
     * @covers \Stanbol\Services\Enhancer\Model\Enhancements::setLanguages
     */
    public function testSetLanguages()
    {
        $langs = array("en", "es");
        $this->enhancements->setLanguages($langs);
        $this->assertCount(2, $this->enhancements->getLanguages());
        $this->assertContains("es", $this->enhancements->getLanguages());
    }

    /**
     * @covers \Stanbol\Services\Enhancer\Model\Enhancements::addLanguage
     */
    public function testAddLanguage()
    {
        $this->enhancements->addLanguage("es");
        $this->assertCount(2, $this->enhancements->getLanguages());
        $this->assertContains("es", $this->enhancements->getLanguages());
    }

    /**
     * @covers \Stanbol\Services\Enhancer\Model\Enhancements::getTextAnnotationsByConfidenceValue
     */
    public function testGetTextAnnotationsByConfidenceValue()
    {
        $result = $this->enhancements->getTextAnnotationsByConfidenceValue(0.5);
        $this->assertCount(23, $result);
    }

    /**
     * @covers \Stanbol\Services\Enhancer\Model\Enhancements::getEntityAnnotationsByConfidenceValue
     */
    public function testGetEntityAnnotationsByConfidenceValue()
    {
        $result = $this->enhancements->getEntityAnnotationsByConfidenceValue(0.5);
        $this->assertCount(40, $result);
    }

    /**
     * @covers \Stanbol\Services\Enhancer\Model\Enhancements::getEntitiesByConfidenceValue
     */
    public function testGetEntitiesByConfidenceValue()
    {
        $result = $this->enhancements->getEntitiesByConfidenceValue(0.5);
        $this->assertCount(40, $result);
    }

    /**
     * @covers \Stanbol\Services\Enhancer\Model\Enhancements::getEntities
     */
    public function testGetEntities()
    {
        $entities = $this->enhancements->getEntities();
        $this->assertCount(39, $entities);
    }

    /**
     * @covers \Stanbol\Services\Enhancer\Model\Enhancements::getEntity
     */
    public function testGetEntity()
    {
        $entity = $this->enhancements->getEntity('http://rdf.freebase.com/ns/m.03d60yx');
        $this->assertNotNull($entity);
        $this->assertEquals('http://rdf.freebase.com/ns/m.03d60yx', $entity->getUri());
        $this->assertNotEmpty($entity->getProperties());
    }

}

?>
