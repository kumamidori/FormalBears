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
namespace FormalBears\Foundation\Config\Loader;

use FormalBears\Foundation\Config\Process\Registry;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;

abstract class AbstractFileLoader extends FileLoader
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param FileLocatorInterface $locator
     * @param Registry             $registry
     */
    public function __construct(FileLocatorInterface $locator, Registry $registry)
    {
        parent::__construct($locator);

        $this->registry = $registry;
    }
}
