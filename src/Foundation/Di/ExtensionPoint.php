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
namespace FormalBears\Foundation\Di;

use Ray\Di\Bind;
use Ray\Di\Container;
use Ray\Di\InjectorInterface;
use FormalBears\Foundation\Di\Exception\UnboundExtensionException;

class ExtensionPoint
{
    /**
     * @var string
     */
    protected $interface;

    /**
     * @var string
     */
    protected $provider;

    /**
     * @var ExtensionCollection
     */
    private $extensionCollection;

    /**
     * @param string $interface
     * @param string $provider
     */
    public function __construct(string $interface = null, string $provider = null)
    {
        $this->interface = $interface;
        $this->provider = $provider;
        $this->extensionCollection = new ExtensionCollection();
    }

    /**
     * @return string
     */
    public function getInterface(): string
    {
        return $this->interface;
    }

    /**
     * @param Extension $extension
     */
    public function addExtension(Extension $extension)
    {
        $this->extensionCollection->add($extension);
    }

    /**
     * @param ExtensionPoint $extensionPoint
     */
    public function merge(self $extensionPoint)
    {
        $this->extensionCollection->merge($extensionPoint->extensionCollection);
    }

    /**
     * @param Container $container
     */
    public function configure(Container $container)
    {
        foreach ([$this->interface, $this->provider] as $property) {
            if ($property === null) {
                throw new \InvalidArgumentException(sprintf('Extension point should be defined, %s', implode(', ', [
                    'interface: '.$this->interface,
                    'provider: '.$this->provider,
                ])));
            }
        }

        (new Bind($container, $this->provider));
        (new Bind($container, $this->interface))->toProvider($this->provider);
        $this->extensionCollection->configure($container, $this->interface, $this->provider);
    }

    /**
     * @param InjectorInterface $injector
     * @param string            $context
     *
     * @return mixed
     */
    public function getExtensionInstance(InjectorInterface $injector, string $context)
    {
        $extension = $this->extensionCollection->get($context);
        if ($extension === null) {
            throw new UnboundExtensionException($context);
        }

        return $extension->getInstance($injector);
    }
}
