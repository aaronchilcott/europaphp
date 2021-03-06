<?php

namespace Europa\View;
use Europa\Config\Config;

class Json implements ViewInterface
{
    private $config = [
        'options' => 0
    ];

    public function __construct($config = [])
    {
        $this->config = new Config($this->config, $config);
    }

    public function render(array $context = array())
    {
        $render = $this->formatParamsToJsonArray($context);
        $render = json_encode($context, $this->config['options']);
        return $render;
    }
    
    private function formatParamsToJsonArray($data)
    {
        $array = array();

        foreach ($data as $name => $item) {
            if (is_array($item) || is_object($item)) {
                $item = $this->formatParamsToJsonArray($item);
            }

            $array[$name] = $item;
        }
        
        return $array;
    }
}