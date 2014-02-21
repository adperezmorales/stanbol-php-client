<?php

namespace Stanbol\Tests\Services\Enhancer;

use Stanbol\Http\ClientFactory;
use Stanbol\Tests\StanbolBaseTestCase;
use Stanbol\Services\Enhancer\StanbolEnhancerServiceImpl;

/**
 * <p>StanbolEnhancerServiceImplTestIT Tests</p>
 *
 * @author Antonio David Pérez Morales <adperezmorales@gmail.com>
 * @covers Stanbol\Services\Enhancer\StanbolEnhancerService
 */
class StanbolEnhancerServiceImplTest extends StanbolBaseTestCase
{

    private $stanbolEnhancer;
    
    /**
     * <p>Mock Enhancer Service</p>
     * @var object
     */
    private $mockEnhancer;

    const TEXT_TO_ENHANCE = "Paris is the capital of France";

    protected function setUp()
    {
        parent::setUp();
        $httpClient = ClientFactory::getInstance()->create(self::STANBOL_ENDPOINT);
        $this->stanbolEnhancer = new StanbolEnhancerServiceImpl($httpClient);
        $this->mockEnhancer = \Phockito::mock('\Stanbol\Services\Enhancer\StanbolEnhancerServiceImpl');
    }

    /**
     * @covers \Stanbol\Services\Enhancer\StanbolEnhancerService::enhance
     */
    public function testEnhance()
    {
        \Phockito::when($this->mockEnhancer->enhance(self::TEXT_TO_ENHANCE))->return(array(1,2,3));
        //$enhancements = $this->stanbolEnhancer->enhance(self::TEXT_TO_ENHANCE);
        $enhancements = $this->mockEnhancer->enhance(self::TEXT_TO_ENHANCE);
        $this->assertNotNull($enhancements);
        $this->assertCount(3, $enhancements);
    }
    
    /**
     * @expectedException \Stanbol\Services\Exception\StanbolServiceException
     */
    public function testFailedEnhancemenet()
    {
        \Phockito::when($this->mockEnhancer->enhance("Bad Text Simulating URL Not Found"))->throw(new \Stanbol\Services\Exception\StanbolServiceException("Enhancement Failed. Http Error Code 400"));
        $this->mockEnhancer->enhance('Bad Text Simulating URL Not Found');
    }

}

?>
