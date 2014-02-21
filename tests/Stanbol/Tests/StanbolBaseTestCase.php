<?php

namespace Stanbol\Tests;

/**
 * <p>Stanbol Client Base Test Case</p>
 *
 * @author Antonio David Pérez Morales <adperezmorales@gmail.com>
 */
class StanbolBaseTestCase extends \PHPUnit_Framework_TestCase
{
    const STANBOL_ENDPOINT = "http://localhost:8080";
    
    /**
     * <p>Called always before executing a test</p>
     */
    protected function setUp()
    {
        parent::setUp();
        $this->assertTrue(true);
    }
    
    /**
     * <p>Called always after executing a test</p>
     */
    protected function tearDown()
    {
        parent::tearDown();
    }
    
    /**
     * <p>Called once before class</p>
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
    }
    
    /**
     * <p>Called once after class</p>
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
    }
}

?>
