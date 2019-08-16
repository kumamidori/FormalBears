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
namespace FormalBears\Foundation\Aop\Matcher;

use Ray\Aop\AbstractMatcher;

class EqualsToMatcher extends AbstractMatcher
{
    /**
     * @var array
     */
    private $values;

    /**
     * @param string|array $values
     */
    public function __construct($values)
    {
        parent::__construct();

        $this->values = (array) $values;
    }

    /**
     * {@inheritdoc}
     */
    public function matchesClass(\ReflectionClass $class, array $arguments)
    {
        return in_array(strtolower($class->getName()), array_map('strtolower', $this->values));
    }

    /**
     * {@inheritdoc}
     */
    public function matchesMethod(\ReflectionMethod $method, array $arguments)
    {
        return in_array(strtolower($method->getShortName()), array_map('strtolower', $this->values));
    }
}
