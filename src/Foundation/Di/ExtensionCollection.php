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

class ExtensionCollection implements IteratorAggregate
{
    /**
     * @var array
     */
    private $extensions = [];

    /**
     * @param string $context
     *
     * @return Extension|null
     */
    public function get(string $context)
    {
        return $this->extensions[$context] ?? null;
    }

    /**
     * @param Extension $extension
     */
    public function add(Extension $extension)
    {
        $this->extensions[$extension->getContext()] = $extension;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->extensions);
    }

    /**
     * @param ExtensionCollection $extensionCollection
     */
    public function merge(self $extensionCollection)
    {
        foreach ($extensionCollection as $extension) { /* @var $extension Extension */
            $existingExtension = $this->get($extension->getContext());
            if ($existingExtension === null) {
                $this->add($extension);
            }
        }
    }

    /**
     * @param Container $container
     * @param string    $interface
     * @param string    $provider
     */
    public function configure(Container $container, string $interface, string $provider)
    {
        foreach ($this as $extension) { /* @var $extension Extension */
            $extension->configure($container, $interface, $provider);
        }
    }
}
