<?php
namespace FormalBears\Foundation\Aop\Matcher;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class FooStub
{
    public function bar()
    {
        
    }
    public function onGet()
    {
    }

    public function onPost()
    {
    }
}

class IsHttpMethodMatcherTest extends TestCase
{
    /**
     * @dataProvider getHttpMethodData
     *
     * @param $method
     */
    public function testMatchesMethodMatchesHttpMethod(ReflectionMethod $method)
    {
        $matcher = new IsHttpMethodMatcher;
        $this->assertTrue($matcher->matchesMethod($method, []), 'method: ' . $method->getName());
    }

    /**
     * @dataProvider getUnrelatedMethodData
     *
     * @param $method
     */
    public function testMatchesMethodUnmatchesUnrelatedMethod(ReflectionMethod $method)
    {
        $matcher = new IsHttpMethodMatcher;
        $this->assertFalse($matcher->matchesMethod($method, []), 'method: ' . $method->getName());
    }

    public function getHttpMethodData()
    {
        return [
            [new ReflectionMethod(FooStub::class, 'onGet')],
            [new ReflectionMethod(FooStub::class, 'onPost')],
        ];
    }

    public function getUnrelatedMethodData()
    {
        return [
            [new ReflectionMethod(FooStub::class, 'bar')],
        ];
    }
}
