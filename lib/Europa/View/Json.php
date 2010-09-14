<?php

/**
 * A view class for rendering JSON data from bound parameters.
 * 
 * @category Views
 * @package  Europa
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  (c) 2010 Trey Shugart
 * @link     http://europaphp.org/license
 */
class Europa_View_Json extends Europa_View
{
    /**
     * Constructs the view and sets parameters.
     * 
     * @param array $params
     */
    public function __construct(array $params = array())
    {
        $this->setParams($params);
    }
    
    /**
     * JSON encodes the parameters on the view and returns them.
     * 
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->getParams());
    }
}