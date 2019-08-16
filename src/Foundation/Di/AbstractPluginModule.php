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

use FormalBears\Foundation\Prioritization\PrioritizableObject;
use Ray\Di\AbstractModule;

abstract class AbstractPluginModule extends AbstractModule
{
    /**
     * @var ExtensionPointCollection
     */
    private $extensionPointCollection;

    /**
     * {@inheritdoc}
     */
    public function __construct(AbstractModule $module = null)
    {
        $this->extensionPointCollection = new ExtensionPointCollection();
        parent::__construct($module);

        if ($module instanceof self) {
            $this->extensionPointCollection->merge($module->extensionPointCollection);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function install(AbstractModule $module)
    {
        parent::install($module);

        if ($module instanceof self) {
            $this->extensionPointCollection->merge($module->extensionPointCollection);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function override(AbstractModule $module)
    {
        parent::override($module);

        if ($module instanceof self) {
            $module->extensionPointCollection->merge($this->extensionPointCollection);
            $this->extensionPointCollection = $module->extensionPointCollection;
        }
    }

    /**
     * @param string $interface
     * @param string $provider
     *
     * @throws \InvalidArgumentException
     */
    protected function defineExtensionPoint(string $interface, string $provider)
    {
        $extensionPoint = $this->extensionPointCollection->get($interface);
        if ($extensionPoint === null) {
            $this->bindExtensionPoint($interface);
        }

        $this->extensionPointCollection->add(new ExtensionPoint($interface, $provider));
    }

    /**
     * @param string $interface
     */
    private function bindExtensionPoint(string $interface)
    {
        $this->bind(ExtensionPoint::class)->annotatedWith($interface)->toProvider(ExtensionPointProvider::class, $interface);
    }

    /**
     * @param string $interface
     * @param string $class
     * @param string $context
     *
     * @throws \InvalidArgumentException
     */
    protected function registerExtension(string $interface, string $class, string $context)
    {
        $extensionPoint = $this->extensionPointCollection->get($interface);
        if ($extensionPoint === null) {
            $extensionPoint = new ExtensionPoint($interface);
            $this->extensionPointCollection->add($extensionPoint);
            $this->bindExtensionPoint($interface);
        }

        $extensionPoint->addExtension(new Extension($interface, $class, $context));
    }

    protected function configureExtensionPoints()
    {
        $this->extensionPointCollection->configure($this->getContainer());
        $this->bind()->annotatedWith('foundation.extension_point_collection')->toInstance($this->extensionPointCollection);
    }
}
