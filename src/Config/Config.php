<?php

namespace Backpack\Install\Config;

use Symfony\Component\Yaml\Yaml;

class Config
{
    public function __construct()
    {
        $this->options = Yaml::parse(file_get_contents(__DIR__ . '/config.yml'));
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


}