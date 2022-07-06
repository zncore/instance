<?php

namespace ZnCore\Base\Tests\Unit;

use ZnCore\Instance\Helpers\InstanceHelper;
use ZnCore\Instance\Libs\Resolvers\InstanceResolver;
use ZnTool\Test\Base\BaseTest;

use App1\ClassSum;
use App1\ClassMultiplication;
use App1\ClassPow;

require_once __DIR__ . '/../classes/ClassSum.php';
require_once __DIR__ . '/../classes/ClassMultiplication.php';
require_once __DIR__ . '/../classes/ClassPow.php';

final class InstanceResolverTest extends BaseTest
{

    public function testCreateAll()
    {
        $instanceResolver = new InstanceResolver();
        /** @var ClassPow $instance */
        $instance = $instanceResolver->create(ClassPow::class);

        $this->assertInstanceOf(ClassPow::class, $instance);
        $this->assertEquals(4, $instance->plus(1,3));

        /** @var ClassMultiplication $instance */
        $instance = $instanceResolver->create(ClassMultiplication::class);
        $this->assertInstanceOf(ClassMultiplication::class, $instance);
        $this->assertEquals(3, $instance->multiplication(1,3));
        $this->assertEquals(60, $instance->multiplication(20,3));

        /** @var ClassPow $instance */
        $instance = $instanceResolver->create(ClassPow::class);
        $this->assertInstanceOf(ClassPow::class, $instance);
        $this->assertEquals(1, $instance->pow(1,3));
        $this->assertEquals(8000, $instance->pow(20,3));
        $this->assertEquals(83521, $instance->pow(17,4));
    }

    public function testCreateInstanceFromClassName()
    {
        $instanceResolver = new InstanceResolver();
        $multiplication = $instanceResolver->create(ClassMultiplication::class);
        /** @var ClassPow $instance */
        $instance = $instanceResolver->create(ClassPow::class, [ClassMultiplication::class => $multiplication]);

        $this->assertInstanceOf(ClassPow::class, $instance);
        $this->assertEquals(4, $instance->plus(1,3));
    }

    public function testCreateInstanceFromIndexArgs()
    {
        $definition = [
            'class' => ClassPow::class,
        ];
        $instanceResolver = new InstanceResolver();

        $multiplication = $instanceResolver->create(ClassMultiplication::class);
        /** @var ClassPow $instance */
        $instance = $instanceResolver->create($definition, [$multiplication]);

        $this->assertInstanceOf(ClassPow::class, $instance);
        $this->assertEquals(4, $instance->plus(1,3));
    }

    public function testCreateInstanceFromNamedArgs()
    {
        $definition = [
            'class' => ClassPow::class,
        ];
        $instanceResolver = new InstanceResolver();

        /** @var ClassMultiplication $multiplication */
        $multiplication = $instanceResolver->create(ClassMultiplication::class);
        /** @var ClassPow $instance */
        $instance = $instanceResolver->create($definition, ['class1' => $multiplication]);

        $this->assertInstanceOf(ClassPow::class, $instance);
        $this->assertEquals(4, $instance->plus(1,3));
    }

    public function testCreateInstanceFromDefinition()
    {
        $definition = [
            'class' => ClassPow::class,
        ];
        $instanceResolver = new InstanceResolver();
        $multiplication = $instanceResolver->create(ClassMultiplication::class);
        /** @var ClassPow $instance */
        $instance = $instanceResolver->create($definition, [ClassMultiplication::class => $multiplication]);

        $this->assertInstanceOf(ClassPow::class, $instance);
        $this->assertEquals(4, $instance->plus(1,3));
    }

    public function testCreateInstanceFromDefinitionWithConstruct()
    {

        $instanceResolver = new InstanceResolver();
        $multiplication = $instanceResolver->create(ClassMultiplication::class);

        $definition = [
            'class' => ClassPow::class,
            '__construct' => [
                ClassMultiplication::class => $multiplication
            ],
        ];
        /** @var ClassPow $instance */
        $instance = $instanceResolver->create($definition);
        $this->assertInstanceOf(ClassPow::class, $instance);
        $this->assertEquals(4, $instance->plus(1,3));
    }

    public function testCallMethodFromIndexArgs()
    {
        $instanceResolver = new InstanceResolver();

        /** @var ClassMultiplication $multiplication */
        $multiplication = $instanceResolver->create(ClassMultiplication::class);
        /** @var ClassPow $instance */
        $instance = $instanceResolver->create(ClassPow::class, [$multiplication]);
        $args = [
            'a' => 7,
            ClassMultiplication::class => $multiplication,
            'b' => 3,
        ];
        $sum = $instanceResolver->callMethod($instance, 'method1', $args);
        $this->assertEquals(10, $sum);

        $args = [1, 3];
        $sum = $instanceResolver->callMethod($instance, 'plus', $args);
        $this->assertEquals(4, $sum);
    }

    public function testCallMethodFromTypeArgs()
    {
        $instanceResolver = new InstanceResolver();

        /** @var ClassMultiplication $multiplication */
        $multiplication = $instanceResolver->create(ClassMultiplication::class);

        /** @var ClassPow $instance */
        $instance = $instanceResolver->create(ClassPow::class, [$multiplication]);
        $args = [
            'a' => 7,
            ClassMultiplication::class => $multiplication,
            'b' => 3,
        ];
        $sum = $instanceResolver->callMethod($instance, 'method1', $args);
        $this->assertEquals(10, $sum);
    }

    public function testCallMethodFromNameArgs()
    {
        $instanceResolver = new InstanceResolver();

        $multiplication = $instanceResolver->create(ClassMultiplication::class);
        /** @var ClassPow $instance */
        $instance = $instanceResolver->create(ClassPow::class, [$multiplication]);
        $args = [
            'a' => 7,
            'class1' => $multiplication,
            'b' => 3,
        ];
        $sum = $instanceResolver->callMethod($instance, 'method1', $args);
        $this->assertEquals(10, $sum);

        $args = [
            'a' => 1,
            'b' => 3,
        ];
        $sum = $instanceResolver->callMethod($instance, 'plus', $args);
        $this->assertEquals(4, $sum);
    }

    public function testCallMethodFromBothArgs()
    {
        $instanceResolver = new InstanceResolver();

        $multiplication = $instanceResolver->create(ClassMultiplication::class);
        /** @var ClassPow $instance */
        $instance = $instanceResolver->create(ClassPow::class, [$multiplication]);
        $args = [
            'a' => 7,
            $multiplication,
            'b' => 3,
        ];
        $sum = $instanceResolver->callMethod($instance, 'method1', $args);
        $this->assertEquals(10, $sum);

        $args = [
            'a' => 7,
            ClassMultiplication::class => $multiplication,
            3,
        ];
        $sum = $instanceResolver->callMethod($instance, 'method1', $args);
        $this->assertEquals(10, $sum);

        $args = [
            7,
            ClassMultiplication::class => $multiplication,
            'b' => 3,
        ];
        $sum = $instanceResolver->callMethod($instance, 'method1', $args);
        $this->assertEquals(10, $sum);

        $args = [
            1,
            'b' => 3,
        ];
        $sum = $instanceResolver->callMethod($instance, 'plus', $args);
        $this->assertEquals(4, $sum);
    }
}
