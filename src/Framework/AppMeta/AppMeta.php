<?php
/*
 * Copyright (c) Atsuhiro Kubo <kubo@iteman.jp>,
 * All rights reserved.
 *
 * This file is part of FormalBears Framework.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace FormalBears\Framework\AppMeta;

final class AppMeta
{
    /**
     * @var string
     */
    public $appNamespace;

    /**
     * @var array
     */
    public $contexts;

    /**
     * @var string
     */
    public $appDir;

    /**
     * @var string
     */
    public $configDir;

    /**
     * @var string
     */
    public $configExtension;

    /**
     * @var string
     */
    public $tmpDir;

    /**
     * @var string
     */
    public $logDir;

    /**
     * @var bool
     */
    public $development;

    /**
     * @param string $appNamespace
     * @param string $context
     * @param string $appDir
     * @param string $confiExtension
     */
    public function __construct(string $appNamespace, string $context, string $appDir, string $confiExtension)
    {
        $this->appNamespace = $appNamespace;
        $this->contexts = array_reverse(explode('-', $context));
        $this->appDir = $appDir;
        $this->configDir = $this->appDir.'/etc/config';
        $this->configExtension = $confiExtension;
        $this->tmpDir = $this->appDir.'/var/tmp/'.$context;
        $this->logDir = $this->appDir.'/var/log';
        $this->development = !in_array('prod', $this->contexts);
    }

    /**
     * @return array
     */
    public function toParameters()
    {
        return [
            'app_meta.app_namespace' => $this->appNamespace,
            'app_meta.contexts' => $this->contexts,
            'app_meta.app_dir' => $this->appDir,
            'app_meta.config_dir' => $this->configDir,
            'app_meta.config_extension' => $this->configExtension,
            'app_meta.tmp_dir' => $this->tmpDir,
            'app_meta.log_dir' => $this->logDir,
            'app_meta.development' => $this->development,
        ];
    }
}
