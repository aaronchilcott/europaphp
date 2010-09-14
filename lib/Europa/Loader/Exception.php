<?php

/**
 * Loader exception class.
 * 
 * @category Exceptions
 * @package  Europa
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  (c) 2010 Trey Shugart
 * @link     http://europaphp.org/license
 */
class Europa_Loader_Exception extends Europa_Exception
{
    /** 
     * Thrown when no load paths are defined and a load is attempted.
     * 
     * @var int
     */
    const NO_PATHS_DEFINED = 1;
    
    /**
     * Thrown when added path cannot be resolved.
     * 
     * @var int
     */
    const INVALID_PATH = 2;
}