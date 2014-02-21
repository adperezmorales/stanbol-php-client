<?php

namespace Stanbol\Tests\Services\Enhancer\Model;

use Stanbol\Services\Enhancer\Model\Parser\EnhancementsParserFactory;
use Stanbol\Services\Enhancer\Model\EntityAnnotation;

/**
 * <p>EntityAnnotation Tests</p>
 *
 * @author Antonio David Pérez Morales <adperezmorales@gmail.com>
 * 
 * @covers \Stanbol\Services\Enhancer\Model\EntityAnnotation
 */
class EntityAnnotationTest extends \PHPUnit_Framework_TestCase
{

    private static $entityAnnotation;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        $enhancements = EnhancementsParserFactory::createDefaultParser(file_get_contents(STANBOL_TESTS_DIR . DIRECTORY_SEPARATOR . 'rdf.txt'))->createEnhancements();
        $entityAnnotations = $enhancements->getEntityAnnotations();
        self::$entityAnnotation = $entityAnnotations['urn:enhancement-0612b7d5-bf5b-62da-9b40-509045ba7651'];
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        self::$entityAnnotation = null;
    }

    /**
     * @covers \Stanbol\Services\Enhancer\Model\Enhancements::getLanguages
     */
    public function testProperties()
    {

        // Test Entity Type
        $this->assertNotNull(self::$entityAnnotation->getEntityTypes());
        $this->assertCount(10, self::$entityAnnotation->getEntityTypes());
        $this->assertContains("http://rdf.freebase.com/ns/common.topic", self::$entityAnnotation->getEntityTypes());

        //Test Extracted From
        $this->assertNotEmpty(self::$entityAnnotation->getExtractedFrom());

        //Test Confidence
        $this->assertEquals(1, self::$entityAnnotation->getConfidence());

        //Test Entity Label
        $this->assertEquals("CMS", self::$entityAnnotation->getEntityLabel());

        //Test Site
        $this->assertEquals("freebase", self::$entityAnnotation->getSite());

        //Test Entity-Reference
        $this->assertNotNull(self::$entityAnnotation->getEntityReference());
        $this->assertTrue(self::$entityAnnotation->getEntityReference() instanceof \Stanbol\Vocabulary\Model\Entity);

        //Test Relations
        $this->assertCount(1, self::$entityAnnotation->getRelations());
        $relations = self::$entityAnnotation->getRelations();
        $firstRelation = array_pop($relations);
        $this->assertTrue($firstRelation instanceof \Stanbol\Services\Enhancer\Model\TextAnnotation);

        //Test Created
        $this->assertNotNull(self::$entityAnnotation->getCreated());
        $this->assertNotEmpty(self::$entityAnnotation->getCreated());

        //Test Creator
        $this->assertEquals("org.apache.stanbol.enhancer.engines.entitylinking.engine.EntityLinkingEngine", self::$entityAnnotation->getCreator());

    }

}

?>
