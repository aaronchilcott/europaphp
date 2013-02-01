<?php

namespace Test\Provider\Controller;
use Europa\Controller\ControllerAbstract;

/**
 * @filter Test\Provider\Controller\ClassFilter Args
 */
class Controller extends ControllerAbstract
{
    public $id;
    
    public $name;
    
    public $notRequired;

    public $classFilter;

    public $methodFilter;
    
    /**
     * @filter Test\Provider\Controller\MethodFilter Args
     */
    public function test($id, $name, $notRequired = true)
    {
        $this->id          = $id;
        $this->name        = $name;
        $this->notRequired = $notRequired;
    }
}