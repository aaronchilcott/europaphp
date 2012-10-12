<?php

namespace Europa\Filter;

/**
 * Returns a camel cased string with the first letter lowercase.
 * 
 * @category Filters
 * @package  Europa
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class LowerCamelCaseFilter
{
    /**
     * Filters the value and returns the filtered value.
     * 
     * @param mixed $value The value to filter.
     * 
     * @return mixed
     */
    public function __invoke($value)
    {
        $ucc   = new UpperCamelCaseFilter;
        $value = $ucc->__invoke($value);
        $value = lcfirst($value);

        return $value;
    }
}