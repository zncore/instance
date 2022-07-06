<?php

namespace App1;

class ClassPow
{

    private $multiplication;

    public function __construct(ClassMultiplication $multiplication)
    {
        $this->multiplication = $multiplication;
    }

    public function plus(int $a, int $b): int
    {
        return $this->multiplication->plus($a, $b);
    }

    public function pow(int $b, int $a): int
    {
        $result = $b;
        for ($i = 0; $i < $a - 1; $i++) {
            $result = $this->multiplication->multiplication($result, $b);
        }
        return $result;
    }

    public function method1(ClassMultiplication $multiplication, int $a, int $b): int
    {
        return $multiplication->plus($a, $b);
    }
}