<?php
/*
 * Copyright (c) Atsuhiro Kubo <kubo@iteman.jp>,
 *               Nana Yamane <shigematsu.nana@gmail.com>,
 * All rights reserved.
 *
 * This file is part of FormalBears.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

declare(strict_types=1);
namespace FormalBears\Foundation\Config\Module;

use FormalBears\Foundation\Config\Definition\ConfigurationInterface;
use FormalBears\Foundation\Config\Process\Registry;
use FormalBears\Foundation\Di\AbstractPluginModule;
use Ray\Di\AbstractModule;

abstract class AbstractConfigAwareModule extends AbstractPluginModule
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Registry            $registry
     * @param AbstractModule|null $module
     */
    public function __construct(Registry $registry, AbstractModule $module = null)
    {
        $this->registry = $registry;
        $configuration = $this->getConfiguration();
        if ($configuration !== null) {
            $this->config = $this->registry->processConfiguration($configuration);
        }

        parent::__construct($module);
    }

    /**
     * @return ConfigurationInterface|null
     */
    protected function getConfiguration()
    {
        return null;
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    protected function createNamedParameters(array $parameters): string
    {
        return implode(',', array_map(function ($name) use (&$parameters) { return $name.'='.$parameters[$name]; }, array_keys($parameters)));
    }
}
