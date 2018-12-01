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

namespace FormalBears\Foundation\Aop\Matcher;

class IsHttpMethodMatcher extends EqualsToMatcher
{
    public function __construct()
    {
        parent::__construct([
            'onGet',
            'onPost',
            'onPut',
            'onPatch',
            'onDelete',
        ]);
    }
}
