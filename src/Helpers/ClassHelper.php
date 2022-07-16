<?php

namespace ZnCore\Instance\Helpers;

use Psr\Container\ContainerInterface;
use ZnCore\Code\Helpers\PropertyHelper;
use ZnCore\Container\Helpers\ContainerHelper;
use ZnCore\Contract\Common\Exceptions\InvalidArgumentException;
use ZnCore\Contract\Common\Exceptions\InvalidConfigException;
use ZnCore\Instance\Exceptions\NotInstanceOfException;

/**
 * Работа с классами
 */
class ClassHelper
{

    /**
     * Проверка существования касса, интерейса или трэйта.
     * @param string $name
     * @return bool
     */
    public static function isExist(string $name): bool
    {
        $name = trim($name, '\\');
        return class_exists($name) || interface_exists($name) || trait_exists($name);
    }

    /**
     * Проверяет, является ли объект инстансом класса/интерфейса
     * @param $instance
     * @param string $interface
     * @param bool $allowString
     * @return bool
     */
    public static function instanceOf($instance, string $interface, bool $allowString = false): bool
    {
        try {
            self::checkInstanceOf($instance, $interface, $allowString);
            return true;
        } catch (NotInstanceOfException $e) {
            return false;
        }
    }

    /**
     * Проверка, является ли объект инстансом класса/интерфейса
     *
     * Если не является, то вызывается исключение.
     *
     * @param $instance
     * @param string $interface
     * @param bool $allowString
     * @throws NotInstanceOfException
     * @throws \ReflectionException
     */
    public static function checkInstanceOf($instance, string $interface, bool $allowString = false): void
    {
        if (empty($instance)) {
            throw new InvalidArgumentException("Argument \"instance\" is empty");
        }
        if (empty($interface)) {
            throw new InvalidArgumentException("Argument \"interfaceClass\" is empty");
        }
        if (!is_object($instance) && !is_string($instance)) {
            throw new InvalidArgumentException("Instance not is object and not is string");
        }
        if (!interface_exists($interface) && !class_exists($interface)) {
            throw new InvalidArgumentException("Interface \"$interface\" not exists");
        }
        if (is_string($instance) && !$allowString) {
            throw new InvalidArgumentException("Instance as string not allowed");
        }

        if (is_string($instance)) {
            $reflection = new \ReflectionClass($instance);
            $interfaces = $reflection->getInterfaces();
            if (!array_key_exists($interface, $interfaces)) {
                self::throwNotInstanceOfException($instance, $interface);
//                throw new NotInstanceOfException("Class \"$instance\" not instanceof \"$interface\"");
            }
        } elseif (!$instance instanceof $interface) {
            self::throwNotInstanceOfException($instance, $interface);
//            $instanceClassName = get_class($instance);
//            throw new NotInstanceOfException("Class \"$instanceClassName\" not instanceof \"$interface\"");
        }
    }

    /**
     * Получить namespace класса
     * @param string $name
     * @return string
     */
    public static function getNamespace(string $name): string
    {
        $name = trim($name, '\\');
        $arr = explode('\\', $name);
        array_pop($arr);
        $name = implode('\\', $arr);
        return $name;
    }

    /**
     * Получить чистое имя класса
     * @param string $class
     * @return false|string
     */
    public static function getClassOfClassName(string $class)
    {
        $lastPos = strrpos($class, '\\');
        $name = substr($class, $lastPos);
        return trim($name, '\\');
    }

    /**
     * Создать объект
     *
     * @param string|object|array $definition Определение
     * @param array $params Параметры конструктора
     * @param ContainerInterface|null $container
     * @return object
     * @throws InvalidConfigException
     * @throws NotInstanceOfException
     */
    public static function createInstance($definition, array $params = [], ContainerInterface $container = null): object
    {
        if (empty($definition)) {
            throw new InvalidConfigException('Empty class config');
        }
        if (is_object($definition)) {
            return $definition;
        }
        $definition = self::normalizeComponentConfig($definition);
        if ($container == null) {
            $container = ContainerHelper::getContainer();
        }
        $instance = $container->make($definition['class'], $params);
        self::configure($instance, $definition);
        return $instance;
    }

    /**
     * Создать объект
     *
     * @param string|object|array $definition Определение
     * @param array $params Атрибуты объекта
     * @return object
     * @throws InvalidConfigException
     * @throws NotInstanceOfException
     */
    public static function createObject($definition, array $params = []): object
    {
        if (empty($definition)) {
            throw new InvalidConfigException('Empty class config');
        }
        if (is_object($definition)) {
            return $definition;
        }
        $definition = self::normalizeComponentConfig($definition);
        $container = ContainerHelper::getContainer();
        $object = $container->make($definition['class']);
        //$object = new $definition['class'];
        /*if ($definition['class']) {
            unset($definition['class']);
        }*/
        self::clearDefinition($definition);

//        EntityHelper::setAttributes($object, $definition);
//        EntityHelper::setAttributes($object, $params);
        self::configure($object, $params);
        self::configure($object, $definition);
        /*if (!empty($interface)) {
            self::checkInstanceOf($object, $interface);
        }*/
        return $object;
    }

    protected static function clearDefinition(array &$properties): void
    {
//        if (!empty($properties)) {
        if (isset($properties['class'])) {
            unset($properties['class']);
        }
//        }
//        return $properties;
    }

    /**
     * Назначить атрибуты сущности из массива
     *
     * @param object $object
     * @param array $properties
     * @return void
     */
    public static function configure(object $object, array $properties): void
    {
        if (!empty($properties)) {
            self::clearDefinition($properties);
            PropertyHelper::setAttributes($object, $properties);
        }
    }

    /**
     * Нормализация описания объекта
     * @param $config
     * @param null $class
     * @return array
     */
    public static function normalizeComponentConfig($config, $class = null): array
    {
        if (empty($config) && empty($class)) {
            return $config;
        }
        if (!empty($class)) {
            $config['class'] = $class;
        }
        if (is_array($config)) {
            return $config;
        }
        if (self::isClass($config)) {
            $config = ['class' => $config];
        }
        return $config;
    }

    /**
     * Проверяет, является ли строка именем класса
     * @param string $name
     * @return bool
     */
    public static function isClass(string $name): bool
    {
        return is_string($name) && (strpos($name, '\\') !== false || class_exists($name));
    }

    private static function throwNotInstanceOfException($instanceClassName, string $interface)
    {
        $instanceClassName = is_object($instanceClassName) ? get_class($instanceClassName) : $instanceClassName;
        throw new NotInstanceOfException("Class \"$instanceClassName\" not instanceof \"$interface\"");
    }
}