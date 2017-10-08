<?php

namespace Gotrecillo\BackpackInstaller\Console;

use Gotrecillo\BackpackInstaller\App;
use Gotrecillo\BackpackInstaller\Config\Config;
use Gotrecillo\BackpackInstaller\Services\Composer;
use Gotrecillo\BackpackInstaller\Services\Customizer;
use Gotrecillo\BackpackInstaller\Services\Environment;
use Gotrecillo\BackpackInstaller\Services\PackageInstaller;
use Gotrecillo\BackpackInstaller\Services\RunProcess;
use League\CLImate\CLImate;
use League\Flysystem\MountManager;
use Symfony\Component\Console\Command\Command;

abstract class BaseCommand extends Command
{
    /**
     * @var App
     */
    protected $app;
    /**
     * @var CLImate
     */
    protected $cli;
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var MountManager
     */
    protected $mountManager;
    /**
     * @var Composer
     */
    protected $composer;
    /**
     * @var Customizer
     */
    protected $customizer;
    /**
     * @var RunProcess
     */
    protected $runProcess;
    /**
     * @var PackageInstaller
     */
    protected $packageInstaller;
    /**
     * @var Environment
     */
    protected $environment;

    protected function setupCommand()
    {
        $this->app = $this->getApplication();
        $this->app->registerServices();
        $this->cli = $this->app->make('cli');
        $this->config = $this->app->make('config');
        $this->mountManager = $this->app->make('mountManager');
        $this->composer = $this->app->make('composer');
        $this->customizer = $this->app->make('customizer');
        $this->runProcess = $this->app->make('runProcess');
        $this->packageInstaller = $this->app->make('packageInstaller');
        $this->environment = $this->app->make('environment');
    }

}