<?php

namespace Stanbol\Tests\Util;

use Stanbol\Tests\StanbolBaseTestCase;
use Stanbol\Util\ClassHelper;

/**
 * <p>ClassHelper Tests</p>
 *
 * @author Antonio David Pérez Morales <adperezmorales@gmail.com>
 * @covers RedLink\Util\ClassHelper
 */
class ClassHelperTest extends StanbolBaseTestCase
{
    /**
     * @covers Stanbol\Util\ClassHelper::getClassName
     */
    public function testClassName() {
        $className = ClassHelper::getClassName($this);
        $this->assertEquals("ClassHelperTest", $className);
    }
}

?>
