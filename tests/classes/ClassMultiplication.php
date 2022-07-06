<?php

namespace App1;

class ClassMultiplication
{
    private $sum;

    public function __construct(ClassSum $sum)
    {
        $this->sum = $sum;
    }

    public function plus(int $a, int $b): int
    {
        return $this->sum->plus($a, $b);
    }

    public function multiplication(int $a, int $b): int
    {
        $result = $b;
        for ($i = 0; $i < $a - 1; $i++) {
            $result = $this->sum->plus($result, $b);
        }
        return $result;
    }
}
