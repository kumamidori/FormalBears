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

use ArrayIterator;
use IteratorAggregate;
use Ray\Di\Container;

class ExtensionPointCollection implements IteratorAggregate
{
    /**
     * @var array
     */
    private $extensionPoints = [];

    /**
     * @param string $interface
     *
     * @return ExtensionPoint|null
     */
    public function get(string $interface)
    {
        return $this->extensionPoints[$interface] ?? null;
    }

    /**
     * @return array
     */
    public function getInterfaces(): array
    {
        return array_keys($this->extensionPoints);
    }

    /**
     * @param ExtensionPoint $extensionPoint
     */
    public function add(ExtensionPoint $extensionPoint)
    {
        $existingExtensionPoint = $this->get($extensionPoint->getInterface());
        if ($existingExtensionPoint !== null) {
            $extensionPoint->merge($existingExtensionPoint);
        }

        $this->extensionPoints[$extensionPoint->getInterface()] = $extensionPoint;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->extensionPoints);
    }

    /**
     * @param ExtensionPointCollection $extensionPointCollection
     */
    public function merge(self $extensionPointCollection)
    {
        foreach ($extensionPointCollection as $extensionPoint) { /* @var $extensionPoint ExtensionPoint */
            $existingExtensionPoint = $this->get($extensionPoint->getInterface());
            if ($existingExtensionPoint === null) {
                $this->add($extensionPoint);
            } else {
                $existingExtensionPoint->merge($extensionPoint);
            }
        }
    }

    /**
     * @param Container $container
     */
    public function configure(Container $container)
    {
        foreach ($this as $extensionPoint) { /* @var $extensionPoint ExtensionPoint */
            $extensionPoint->configure($container);
        }
    }
}
