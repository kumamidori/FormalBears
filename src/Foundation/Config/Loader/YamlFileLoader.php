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

namespace FormalBears\Foundation\Config\Loader;

use FormalBears\Foundation\Config\Process\Registry;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

class YamlFileLoader extends AbstractFileLoader
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * {@inheritdoc}
     */
    public function __construct(FileLocatorInterface $locator, Registry $registry)
    {
        parent::__construct($locator, $registry);

        $this->parser = new Parser();
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $path = $this->locator->locate($resource);
        $content = $this->loadResource($path);
        if ($content === null) {
            return;
        }

        $this->loadImports($content, $path);
        $this->loadParameters($content);
        $this->loadBody($content);

        $this->setCurrentDir(dirname($path));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        if (!is_string($resource)) {
            return false;
        }

        if ($type === null && in_array(pathinfo($resource, PATHINFO_EXTENSION), ['yaml', 'yml'], true)) {
            return true;
        }

        return in_array($type, ['yaml', 'yml'], true);
    }

    /**
     * @param $resource
     *
     * @throws YamlSyntaxException
     */
    private function loadResource($resource)
    {
        try {
            return $this->parser->parse(file_get_contents($resource), Yaml::PARSE_CONSTANT | Yaml::PARSE_CUSTOM_TAGS);
        } catch (ParseException $e) {
            throw new YamlSyntaxException(sprintf('The configuration is not valid YAML in "%s".', $resource), 0, $e);
        }
    }

    /**
     * @param array $content
     * @param $path
     *
     * @throws YamlSyntaxException
     */
    private function loadImports(array $content, $path)
    {
        if (!isset($content['imports'])) {
            return;
        }

        if (!is_array($content['imports'])) {
            throw new YamlSyntaxException(sprintf('The "imports" should be an array, "%s" is specified.', gettype($content['imports'])));
        }

        $defaultDirectory = dirname($path);
        foreach ($content['imports'] as $import) {
            if (!is_array($import)) {
                throw new YamlSyntaxException(sprintf('Each value in "imports" should be an array, "%s" is specified.', gettype($import)));
            }

            $this->setCurrentDir($defaultDirectory);
            $this->import($import['resource'], $import['type'] ?? null, isset($import['ignore_errors']) ? (bool) $import['ignore_errors'] : false, $path);
        }
    }

    /**
     * @param array $content
     *
     * @throws YamlSyntaxException
     */
    private function loadParameters(array $content)
    {
        if (!isset($content['parameters'])) {
            return;
        }

        if (!is_array($content['parameters'])) {
            throw new YamlSyntaxException(sprintf('The "parameters" should be an array, "%s" is specified.', gettype($content['parameters'])));
        }

        foreach ($content['parameters'] as $key => $value) {
            $this->registry->setParameter($key, $value);
        }
    }

    /**
     * @param $config
     */
    private function loadBody($config)
    {
        foreach ($config as $namespace => $namespaceConfig) {
            if (in_array($namespace, ['imports', 'parameters'])) {
                continue;
            }

            $this->registry->set($namespace, is_array($namespaceConfig) ? $namespaceConfig : []);
        }
    }
}
