<?php

namespace georgkott\verybigandexactnumbers\operation;

abstract class OperationAbstract
{
    protected $n1;  //natural1
    protected $r1;  //rational1
    protected $n2;  //natural2
    protected $r2;  //rational2

    protected $resultNatural;
    protected $resultRational;

    public function __construct($n1 = '', $r1 = '', $n2 = '', $r2 = '')
    {
        $this->n1 = $n1;
        $this->r1 = $r1;

        $this->n2 = $n2;
        $this->r2 = $r2;
    }
}

