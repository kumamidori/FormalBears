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
namespace FormalBears\Foundation\Config\Definition;

use Symfony\Component\Config\Definition\ConfigurationInterface as BaseConfigurationInterface;

interface ConfigurationInterface extends BaseConfigurationInterface
{
    /**
     * @return string
     */
    public function getNamespace(): string;
}
