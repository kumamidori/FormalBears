<?php
/*
 * Copyright (c) Atsuhiro Kubo <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of FormalBears.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

declare(strict_types=1);
namespace FormalBears\Foundation\Di;

use Ray\Di\Bind;
use Ray\Di\Container;
use Ray\Di\InjectorInterface;

class Extension
{
    /**
     * @var string
     */
    private $interface;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $context;

    /**
     * @param string $interface
     * @param string $class
     * @param string $context
     */
    public function __construct(string $interface, string $class, string $context)
    {
        $this->interface = $interface;
        $this->class = $class;
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getContext(): string
    {
        return $this->context;
    }

    /**
     * @param Container $container
     * @param string    $interface
     * @param string    $provider
     */
    public function configure(Container $container, string $interface, string $provider)
    {
        (new Bind($container, $this->class));
        (new Bind($container, $interface))->annotatedWith($this->context)->toProvider($provider, $this->context);
    }

    /**
     * @param InjectorInterface $injector
     *
     * @return mixed
     */
    public function getInstance(InjectorInterface $injector)
    {
        return $injector->getInstance($this->class);
    }
}
