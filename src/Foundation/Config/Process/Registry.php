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

namespace FormalBears\Foundation\Config\Process;

use FormalBears\Foundation\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Exception\EnvNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\EnvPlaceholderParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class Registry
{
    /**
     * @var ParameterBagInterface
     */
    private $configs;

    /**
     * @var ParameterBagInterface
     */
    private $parameters;

    /**
     * @var Processor
     */
    private $processor;

    /**
     * @param ParameterBagInterface $parameters
     */
    public function __construct(ParameterBagInterface $parameters)
    {
        $this->processor = new Processor();
        $this->configs = new ParameterBag();
        $this->parameters = $parameters;
    }

    /**
     * @param string $namespace
     * @param array  $config
     */
    public function set(string $namespace, array $config)
    {
        if ($this->configs->has($namespace)) {
            $namespaceConfigs = $this->configs->get($namespace);
        } else {
            $namespaceConfigs = [];
        }

        $namespaceConfigs[] = $config;

        $this->configs->set($namespace, $namespaceConfigs);
    }

    /**
     * @param string $namespace
     *
     * @return array
     */
    public function get(string $namespace): array
    {
        return $this->configs->has($namespace) ? $this->configs->get($namespace) : [];
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setParameter(string $name, $value)
    {
        $this->parameters->set($name, $value);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getParameter(string $name)
    {
        return $this->parameters->get($name);
    }

    public function freeze()
    {
        $this->configs = new FrozenParameterBag($this->configs->all());
        $this->parameters->resolve();
        if ($this->parameters instanceof EnvPlaceholderParameterBag) {
            $this->parameters = new FrozenParameterBag($this->resolveEnvPlaceHolders($this->resolveEnvPlaceHolders($this->parameters->all())));
        }
    }

    /**
     * @param ConfigurationInterface $configuration
     *
     * @return array
     */
    public function processConfiguration(ConfigurationInterface $configuration)
    {
        $config = $this->processor->processConfiguration(
            $configuration,
            $this->configs->has($configuration->getNamespace()) ? $this->resolveParameterPlaceHolders($this->configs->get($configuration->getNamespace())) : []
        );

        return $this->resolveParameterPlaceHolders($config);
    }

    /**
     * @param mixed $config
     *
     * @return mixed
     */
    private function resolveParameterPlaceHolders($config)
    {
        if (is_string($config)) {
            return $this->parameters->resolveValue($config);
        } elseif (is_array($config)) {
            foreach ($config as $key => $value) {
                $resolvedValue = $this->resolveParameterPlaceHolders($value);
                if ($resolvedValue !== $value) {
                    $config[$key] = $resolvedValue;
                }
            }

            return $config;
        } else {
            return $config;
        }
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function resolveEnvPlaceHolders($value)
    {
        if (is_string($value)) {
            $value = $this->parameters->resolveValue($value);

            $parameters = $this->parameters; /* @var $parameters EnvPlaceholderParameterBag */
            foreach ($parameters->getEnvPlaceholders() as $env => $placeholders) {
                foreach ($placeholders as $placeholder) {
                    if (stripos($value, $placeholder) !== false) {
                        $resolvedValue = $parameters->escapeValue($this->getEnv($env));
                        $value = str_ireplace($placeholder, $resolvedValue, $value);
                    }
                }
            }

            return $value;
        } elseif (is_array($value)) {
            $result = array();
            foreach ($value as $k => $v) {
                $result[$this->resolveEnvPlaceholders($k)] = $this->resolveEnvPlaceholders($v);
            }

            return $result;
        } else {
            return $value;
        }
    }

    /**
     * @param string $config
     *
     * @return string
     *
     * @throws EnvNotFoundException
     */
    private function getEnv(string $config): string
    {
        if (array_key_exists($config, $_ENV)) {
            return $_ENV[$config];
        }

        $env = getenv($config);
        if ($env !== false) {
            return $env;
        }

        if (!$this->parameters->has("env($config)")) {
            throw new EnvNotFoundException($config);
        }

        return $this->parameters->get("env($config)");
    }
}
