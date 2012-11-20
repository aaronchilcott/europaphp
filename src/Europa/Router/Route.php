<?php

namespace Europa\Router;
use Europa\Config\Config;
use Europa\Exception\Exception;
use Europa\Filter\ClassNameFilter;
use Europa\Request\CliInterface;
use Europa\Request\HttpInterface;
use Europa\Request\RequestInterface;

/**
 * Default request route implementation.
 * 
 * @category Router
 * @package  Europa
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class Route
{
    /**
     * The default controller parameter name.
     * 
     * @var string
     */
    const CONTROLLER = 'controller';

    /**
     * The default route configuration.
     * 
     * @var array
     */
    private $config = [
        'match'             => '^$',
        'method'            => 'get',
        'format'            => ':controller/:action',
        'params'            => ['controller' => 'index', 'action' => 'get'],
        'controller.prefix' => 'Controller\\',
        'controller.suffix' => ''
    ];

    /**
     * Constructs and configures a new route.
     * 
     * @param mixed $config The route configuration.
     * 
     * @return Route
     */
    public function __construct($config)
    {
        $this->config = new Config($this->config, $config);

        if (!$this->config->controller) {
            Exception::toss('The route "%s" did not provide a controller class name.', $this->config->expression);
        }
    }

    /**
     * Routes the specified request if it matches the route.
     * 
     * @param string           $name    The route name. This is determined by the router invoking the route.
     * @param RequestInterface $request The request to route. This is also determined by the router invoking the route.
     * 
     * @return false | Object
     */
    public function __invoke($name, RequestInterface $request)
    {
        // Guilty until proven innocent.
        $matches = false;

        // Allow both HTTP and CLI requests to be routed.
        if ($request instanceof HttpInterface) {
            $matches = $this->handleHttpRequest($request);
        } elseif ($request instanceof CliInterface) {
            $matches = $this->handleCliRequest($request);
        }

        // If nothing was matched, the route failed.
        if (!$matches) {
            return false;
        }

        // The first match is the whole request; we don't use this.
        array_shift($matches);

        // Set defaults and matches from the route expression.
        $request->setParams($this->config->params);
        $request->setParams($matches);

        // A specified controller class overrides the "controller" parameter in the request.
        $controller = $this->resolveController($request);
        
        // Ensure the controller exists.
        if (!class_exists($controller)) {
            Exception::toss('The controller class "%s" given for route "%s" does not exist.', $controller, $name);
        }

        return new $controller;
    }

    /**
     * Allows the route to be reverse engineered. Good if you don't want to have to re-write your URLs if you update your routes.
     * 
     * @param array $params The parameters to format with.
     * 
     * @return string.
     */
    public function format(array $params = [])
    {
        $uri    = $this->config->format;
        $params = array_merge($this->config->defaults->export(), $params);

        foreach ($params as $name => $value) {
            $uri = str_replace(':' . $name, $value);
        }

        return $uri;
    }

    /**
     * Handles a request using the HTTP interface.
     * 
     * @param RequestInterface $request The request being matched.
     * 
     * @return array | false
     */
    private function handleHttpRequest(HttpInterface $request)
    {
        if ($this->config->method !== $request->getMethod()) {
            return false;
        }

        if (!preg_match('!' . $this->config->match . '!', $request->getUri()->getRequest(), $matches)) {
            return false;
        }

        return $matches;
    }

    /**
     * Handles a request using the CLI interface.
     * 
     * @param RequestInterface $request The request being matched.
     * 
     * @return array | false
     */
    private function handleCliRequest(CliInterface $request)
    {
        if (!$this->config->match) {
            return false;
        }

        if ($this->config->method !== 'cli') {
            return false;
        }

        if (!preg_match('!' . $this->config->match . '!', $request->getCommand(), $matches)) {
            return false;
        }

        return $matches;
    }

    /**
     * Formats a controller name from the request if no "controller" configuration option is specified.
     * 
     * @param RequestInterface $request The request to resolve the controller from if none is specified in the config.
     * 
     * @return string
     */
    private function resolveController(RequestInterface $request)
    {
        return (new ClassNameFilter($this->config->controller))->__invoke($request->getParam(self::CONTROLLER));
    }
}