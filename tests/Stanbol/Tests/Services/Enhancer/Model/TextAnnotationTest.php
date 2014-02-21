<?php

namespace Stanbol\Tests\Services\Enhancer\Model;

use Stanbol\Services\Enhancer\Model\Parser\EnhancementsParserFactory;

/**
 * <p>TextAnnotation Tests</p>
 *
 * @author Antonio David Pérez Morales <adperezmorales@gmail.com>
 * 
 * @covers \Stanbol\Services\Enhancer\Model\TextAnnotation
 */
class TextAnnotationTest extends \PHPUnit_Framework_TestCase
{

    private static $model;
    private static $textAnnotation;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        $enhancements = EnhancementsParserFactory::createDefaultParser(file_get_contents(STANBOL_TESTS_DIR . DIRECTORY_SEPARATOR . 'rdf.txt'))->createEnhancements();
        $textAnnotations = $enhancements->getTextAnnotations();

        self::$textAnnotation = $textAnnotations['urn:enhancement-5ed22ff7-0c2f-4ad5-ee87-b71968f613a8'];
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        self::$model = null;
        self::$textAnnotation = null;
    }

    /**
     * @covers \Stanbol\Services\Enhancer\Model\Enhancements::getLanguages
     */
    public function testProperties()
    {

        // Test Selection Context
        $this->assertNotEmpty(self::$textAnnotation->getSelectionContext());
        $this->assertEquals("The System is well integrated with many CMS like Drupal and Alfresco.", self::$textAnnotation->getSelectionContext());

        //Test Language
        $this->assertEquals("en", self::$textAnnotation->getLanguage());

        //Test Extracted From
        $this->assertNotEmpty(self::$textAnnotation->getExtractedFrom());

        //Test Confidence
        $this->assertEquals(1, self::$textAnnotation->getConfidence());

        //Test Selected Text
        $this->assertEquals("The System", self::$textAnnotation->getSelectedText());

        //Test Start
        $this->assertEquals(391, self::$textAnnotation->getStarts());

        //Test End
        $this->assertEquals(401, self::$textAnnotation->getEnds());

        //Test Created
        $this->assertNotNull(self::$textAnnotation->getCreated());
        $this->assertNotEmpty(self::$textAnnotation->getCreated());

        //Test Creator
        $this->assertEquals("org.apache.stanbol.enhancer.engines.entitylinking.engine.EntityLinkingEngine", self::$textAnnotation->getCreator());

        //Test Relations
        $this->assertNull(self::$textAnnotation->getRelations());
    }

}

?>
