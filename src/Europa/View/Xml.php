<?php

namespace Europa\View;
use Europa\Config\Config;
use Europa\Filter\ToStringFilter;

/**
 * A view class for rendering JSON data from bound parameters.
 * 
 * @category Views
 * @package  Europa
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class Xml
{
    /**
     * Configuration array.
     * 
     * @var array
     */
    private $config = [
        'declare'        => true,
        'encoding'       => 'UTF-8',
        'indent'         => true,
        'numericKeyName' => 'item',
        'rootNode'       => 'xml',
        'spaces'         => 2,
        'version'        => '1.0'
    ];

    /**
     * The filter used to turn an item value into a string.
     * 
     * @var ToStringFilter
     */
    private $toStringFilter;
    
    /**
     * Sets up the XML view renderer.
     * 
     * @param string $config The configuration array.
     * 
     * @return Xml
     */
    public function __construct(array $config = [])
    {
        $this->config         = new Config($this->config, $config);
        $this->toStringFilter = new ToStringFilter;
    }
    
    /**
     * JSON encodes the parameters on the view and returns them.
     * 
     * @return string
     */
    public function __invoke(array $context = [])
    {
        $str = '';
        
        // Build the XML declaration if enabled.
        if ($this->config->declare) {
            $str = '<?xml version="'
                . $this->toStringFilter->__invoke($this->config->version)
                . '" encoding="'
                . $this->config->encoding
                . '" ?>'
                . PHP_EOL;
        }

        // Render a root node if given a name.
        if ($this->config->rootNode) {
            $context = [$this->config->rootNode => $context];
        }
        
        // Render the XML tree.
        foreach ($context as $name => $content) {
            $str .= $this->renderNode($name, $content);
        }
        
        // Remove whitespace before returning.
        return trim($str);
    }
    
    /**
     * Renders a particular node.
     * 
     * @param string $name    The node name.
     * @param string $content The node content.
     * 
     * @return string
     */
    private function renderNode($name, $content, $level = 0)
    {
        $keys = $this->config->numericKeyName;
        
        // translate a numeric key to a replacement key
        if (is_numeric($name)) {
            if (is_string($keys)) {
                $name = $keys;
            } elseif (is_array($keys) && isset($keys[$level])) {
                $name = $keys[$level];
            }
        }
        
        // indent the node
        $ind = $this->indent($level);
        $str = $ind . "<{$name}>";
        
        // render child nodes if the value is traversable
        if (is_array($content) || is_object($content)) {
            $str .= PHP_EOL;

            foreach ($content as $k => $v) {
                $str .= $this->renderNode($k, $v, $level + 1);
            }

            $str .= $ind;
        } else {
            $str .= $this->toStringFilter->__invoke($content);
        }
        
        $str .= "</{$name}>";
        $str .= PHP_EOL;
        
        return $str;
    }
    
    /**
     * Indents the XML using the specified configuration settings.
     * 
     * @param int $level The level to indent to.
     * 
     * @return string
     */
    private function indent($level)
    {
        $indent = $this->config->spaces;
        $indent = $indent ? str_repeat(' ', $indent) : "\t";
        return str_repeat($indent, $level);
    }
}