<?php

namespace ZnCore\Base\Tests\Unit;

use ZnCore\Instance\Helpers\InstanceHelper;
use ZnTool\Test\Base\BaseTest;

class Class1
{

    public function plus(int $a, int $b): int
    {
        return $a + $b;
    }
}

class Class2
{

    private $class1;

    public function __construct(Class1 $class1)
    {
        $this->class1 = $class1;
    }

    public function plus(int $a, int $b): int
    {
        return $this->class1->plus($a, $b);
    }

    public function method1(Class1 $class1, int $a, int $b): int
    {
        return $class1->plus($a, $b);
    }
}

final class InstanceHelperTest extends BaseTest
{

    public function testCreateInstanceFromClassName()
    {
        $instance = InstanceHelper::create(Class2::class, [Class1::class => new Class1()]);

        $this->assertInstanceOf(Class2::class, $instance);
        $this->assertEquals(4, $instance->plus(1,3));
    }

    public function testCreateInstanceFromIndexArgs()
    {
        $definition = [
            'class' => Class2::class,
        ];
        $instance = InstanceHelper::create($definition, [new Class1()]);

        $this->assertInstanceOf(Class2::class, $instance);
        $this->assertEquals(4, $instance->plus(1,3));
    }

    public function testCreateInstanceFromNamedArgs()
    {
        $definition = [
            'class' => Class2::class,
        ];
        $instance = InstanceHelper::create($definition, ['class1' => new Class1()]);

        $this->assertInstanceOf(Class2::class, $instance);
        $this->assertEquals(4, $instance->plus(1,3));
    }

    public function testCreateInstanceFromDefinition()
    {
        $definition = [
            'class' => Class2::class,
        ];
        $instance = InstanceHelper::create($definition, [Class1::class => new Class1()]);

        $this->assertInstanceOf(Class2::class, $instance);
        $this->assertEquals(4, $instance->plus(1,3));
    }

    public function testCreateInstanceFromDefinitionWithConstruct()
    {
        $definition = [
            'class' => Class2::class,
            '__construct' => [
                Class1::class => new Class1()
            ],
        ];
        $instance = InstanceHelper::create($definition);
        $this->assertInstanceOf(Class2::class, $instance);
        $this->assertEquals(4, $instance->plus(1,3));
    }

    /*public function testCallMethodFromIndexArgs()
    {
        $instance = InstanceHelper::create(Class2::class, [new Class1()]);
        $args = [
            'a' => 7,
            Class1::class => new Class1,
            'b' => 3,
        ];
        $sum = InstanceHelper::callMethod($instance, 'method1', $args);
        $this->assertEquals(10, $sum);

        $args = [1, 3];
        $sum = InstanceHelper::callMethod($instance, 'plus', $args);
        $this->assertEquals(4, $sum);
    }

    public function testCallMethodFromTypeArgs()
    {
        $instance = InstanceHelper::create(Class2::class, [new Class1()]);
        $args = [
            'a' => 7,
            Class1::class => new Class1,
            'b' => 3,
        ];
        $sum = InstanceHelper::callMethod($instance, 'method1', $args);
        $this->assertEquals(10, $sum);
    }

    public function testCallMethodFromNameArgs()
    {
        $instance = InstanceHelper::create(Class2::class, [new Class1()]);
        $args = [
            'a' => 7,
            'class1' => new Class1,
            'b' => 3,
        ];
        $sum = InstanceHelper::callMethod($instance, 'method1', $args);
        $this->assertEquals(10, $sum);

        $args = [
            'a' => 1,
            'b' => 3,
        ];
        $sum = InstanceHelper::callMethod($instance, 'plus', $args);
        $this->assertEquals(4, $sum);
    }

    public function testCallMethodFromBothArgs()
    {
        $instance = InstanceHelper::create(Class2::class, [new Class1()]);
        $args = [
            'a' => 7,
            new Class1,
            'b' => 3,
        ];
        $sum = InstanceHelper::callMethod($instance, 'method1', $args);
        $this->assertEquals(10, $sum);

        $args = [
            'a' => 7,
            Class1::class => new Class1,
            3,
        ];
        $sum = InstanceHelper::callMethod($instance, 'method1', $args);
        $this->assertEquals(10, $sum);

        $args = [
            7,
            Class1::class => new Class1,
            'b' => 3,
        ];
        $sum = InstanceHelper::callMethod($instance, 'method1', $args);
        $this->assertEquals(10, $sum);

        $args = [
            1,
            'b' => 3,
        ];
        $sum = InstanceHelper::callMethod($instance, 'plus', $args);
        $this->assertEquals(4, $sum);
    }*/
}
