<?php

class Test_Request_Params_Cli extends Testes_Test
{
    public function setUp()
    {
        // simulate argv
        $_SERVER['argv'] = array(
            'test-script.php',
            '-f',
            '-flag2',
            '--flag3',
            '-p', 'param1',
            '--param2', 'param2',
            '--param3', 'param3',
            '-param3', 'overridden',
            '--controller', 'customcontroller'
        );
        $this->request = new \Europa\Request\Cli;
    }
    
    public function testCliFlag1()
    {
        $this->assert(
            $this->request->f === true,
            'Flags not working.'
        );
    }
    
    public function testCliFlag2()
    {
        $this->assert(
            $this->request->flag2 === true,
            'Flags not working.'
        );
    }
    
    public function testCliFlag3()
    {
        $this->assert(
            $this->request->flag3 === true,
            'Flags not working.'
        );
    }
    
    public function testCliParam1()
    {
        $this->assert(
            $this->request->p === 'param1',
            'Named paramters not working.'
        );
    }
    
    public function testCliParam2()
    {
        $this->assert(
            $this->request->param2 === 'param2',
            'Named paramters not working.'
        );
    }
    
    public function testCliParam3()
    {
        $this->assert(
            $this->request->param3 === 'overridden',
            'Named paramters not working.'
        );
    }
    
    public function testCliControllerSetting()
    {
        $this->assert(
            $this->request->getController() === 'customcontroller',
            'Controller setting not working.'
        );
    }
    
    public function testCliParamRemoving()
    {
        $this->request->clear();
        $this->assert(
            $this->request->param3 === null,
            'Parameter removing not working'
        );
    }
}