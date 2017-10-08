<?php

namespace Gotrecillo\BackpackInstaller\Config;

use Symfony\Component\Yaml\Yaml;

class Config
{
    private $file;

    public function __construct()
    {
        $this->file = __DIR__ . '/config.yml';
        $this->options = Yaml::parse(file_get_contents($this->file));
    }

    public function getName()
    {
        return $this->options['name'];
    }

    public function getVersion()
    {
        return $this->options['version'];
    }

    public function getAllPackages()
    {
        return array_merge(
            $this->getPackages(),
            $this->options['dependant_packages'],
            $this->getDefaultPackages()
        );
    }

    public function getPackages()
    {
        return $this->options['packages'];
    }

    public function getDefaultPackages()
    {
        return $this->options['default_packages'];
    }

    public function getOptions()
    {
        return $this->options['options'];
    }

    public function getOption($key)
    {
        return $this->options['options'][$key];
    }

    public function updateOption($key, $value)
    {
        $this->options['options'][$key] = $value;

        $yaml = Yaml::dump($this->options);

        file_put_contents($this->file, $yaml);
    }
}