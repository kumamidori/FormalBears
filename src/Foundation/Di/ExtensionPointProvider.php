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

use Ray\Di\Di\Named;
use Ray\Di\ProviderInterface;
use Ray\Di\SetContextInterface;

class ExtensionPointProvider implements ProviderInterface, SetContextInterface
{
    /**
     * @var ExtensionPointCollection
     */
    private $extensionPointCollection;

    /**
     * @var string
     */
    private $interface;

    /**
     * @param ExtensionPointCollection $extensionPointCollection
     *
     * @Named("extensionPointCollection=foundation.extension_point_collection")
     */
    public function __construct($extensionPointCollection)
    {
        $this->extensionPointCollection = $extensionPointCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext($context)
    {
        $this->interface = $context;
    }

    /**
     * {@inheritdoc}
     *
     * @return ExtensionPoint
     */
    public function get(): ExtensionPoint
    {
        return $this->extensionPointCollection->get($this->interface);
    }
}
