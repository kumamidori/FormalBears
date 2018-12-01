<?php
/*
 * Copyright (c) Atsuhiro Kubo <kubo@iteman.jp>,
 *               Nana Yamane <shigematsu.nana@gmail.com>,
 * All rights reserved.
 *
 * This file is part of FormalBears Framework.
 *
 * This program and the accompanying materials are made available under
 * the terms of the BSD 2-Clause License which accompanies this
 * distribution, and is available at http://opensource.org/licenses/BSD-2-Clause
 */

namespace FormalBears\Framework\Module;

use FormalBears\Foundation\Config\Loader\YamlFileLoader;
use FormalBears\Foundation\Config\Module\AbstractConfigAwareModule;
use FormalBears\Foundation\Config\Process\Registry;
use FormalBears\Framework\AppMeta\AppMeta;
use Ray\Di\AbstractModule;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\GlobFileLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ParameterBag\EnvPlaceholderParameterBag;
use Symfony\Component\Dotenv\Dotenv;

abstract class AbstractAppModule extends AbstractConfigAwareModule
{
    /**
     * @var AppMeta
     */
    protected $appMeta;

    /**
     * @param AbstractModule|null $module
     */
    public function __construct(AbstractModule $module = null)
    {
        $context = isset($GLOBALS['context']) ? $GLOBALS['context'] : 'app';
        $this->appMeta = new AppMeta($this->getAppNamespace(), $context, $this->getAppDir(), $this->getConfigExtension());
        $registry = new Registry(new EnvPlaceholderParameterBag($this->appMeta->toParameters()));
        $this->loadDotenv();
        $this->loadConfiguration($this->createConfigLoader($registry));
        $registry->freeze();

        parent::__construct($registry, $module);

        $this->bind(AppMeta::class)->toInstance($this->appMeta);
    }

    protected function loadDotenv()
    {
        $dotenv = new Dotenv();
        $dotenv->load($this->appMeta->appDir.'/.env');

        foreach ($this->appMeta->contexts as $context) {
            if (file_exists($this->appMeta->appDir.'/.env.'.$context)) {
                $dotenv->load($this->appMeta->appDir.'/.env.'.$context);
            }
        }
    }

    /**
     * @param LoaderInterface $loader
     */
    protected function loadConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->appMeta->configDir.'/modules/*'.$this->appMeta->configExtension, 'glob');

        foreach ($this->appMeta->contexts as $context) {
            if (is_dir($this->appMeta->configDir.'/modules/'.$context)) {
                $loader->load($this->appMeta->configDir.'/modules/'.$context.'/**/*'.$this->appMeta->configExtension, 'glob');
            }

            $loader->load($this->appMeta->configDir.'/contexts/'.$context.$this->appMeta->configExtension, 'glob');
        }
    }

    /**
     * @param Registry $registry
     *
     * @return LoaderInterface
     */
    protected function createConfigLoader(Registry $registry): LoaderInterface
    {
        $locator = new FileLocator();
        $loader = new GlobFileLoader($locator);
        $loader->setResolver(new LoaderResolver([
            new YamlFileLoader($locator, $registry),
        ]));

        return $loader;
    }

    /**
     * @return string
     */
    abstract protected function getAppNamespace(): string;

    /**
     * @return string
     */
    abstract protected function getAppDir(): string;

    /**
     * @return string
     */
    abstract protected function getConfigExtension(): string;
}
