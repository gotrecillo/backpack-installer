<?php

namespace Gotrecillo\BackpackInstaller\Packages;

use Gotrecillo\BackpackInstaller\App;
use Gotrecillo\BackpackInstaller\Interfaces\HasEnvironmentVars;
use Gotrecillo\BackpackInstaller\Interfaces\HasMenuItems;
use Gotrecillo\BackpackInstaller\Interfaces\HasPostInstall;
use Gotrecillo\BackpackInstaller\Interfaces\HasPublishableAssets;
use Gotrecillo\BackpackInstaller\Interfaces\Package as PackageInterface;
use Gotrecillo\BackpackInstaller\Services\Artisan;
use Gotrecillo\BackpackInstaller\Services\Composer;
use Gotrecillo\BackpackInstaller\Services\Environment;
use Gotrecillo\BackpackInstaller\Services\FileModifier;
use Gotrecillo\BackpackInstaller\Services\PackageInstaller;
use Gotrecillo\BackpackInstaller\Services\Provider;
use Gotrecillo\BackpackInstaller\Services\RunProcess;
use Gotrecillo\BackpackInstaller\Services\SideBarUpdater;
use League\CLImate\CLImate;
use League\Flysystem\MountManager;

abstract class Package implements PackageInterface
{
    protected $app;
    protected $name;
    protected $slug;
    /**
     * @var Artisan
     */
    protected $artisan;
    /**
     * @var Composer
     */
    protected $composer;
    /**
     * @var MountManager
     */
    protected $mountManager;
    /**
     * @var PackageInstaller
     */
    protected $packageInstaller;
    /**
     * @var RunProcess
     */
    protected $runProcess;
    /**
     * @var SideBarUpdater
     */
    protected $sidebarUpdater;
    /**
     * @var FileModifier
     */
    protected $fileModifier;
    /**
     * @var Environment
     */
    protected $environment;
    /**
     * @var CLImate
     */
    protected $cli;
    /**
     * @var Provider
     */
    protected $provider;


    public function __construct(App $app)
    {
        $this->app = $app;
        $this->artisan = $app->make('artisan');
        $this->composer = $app->make('composer');
        $this->runProcess = $app->make('runProcess');
        $this->packageInstaller = $app->make('packageInstaller');
        $this->mountManager = $app->make('mountManager');
        $this->sidebarUpdater = $app->make('sideBarUpdater');
        $this->fileModifier = $app->make('fileModifier');
        $this->environment = $app->make('environment');
        $this->provider = $app->make('provider');
        $this->cli = $app->make('cli');
        $this->setup();
    }

    public function install()
    {
        $command = $this->composer->getAddPackageCommand($this->getSlug());
        $this->runProcess->run($command);

        if ($this instanceof HasPublishableAssets) {
            $this->prePublish();
            $publishableOptions = $this->getPublishableOptions();
            $this->publishAssets($publishableOptions);
        }
        if ($this instanceof HasMenuItems) {
            $items = $this->getMenuItems();
            $this->addMenuItems($items);
        }
        if ($this instanceof HasEnvironmentVars) {
            $changes = $this->getEnvironmentChanges();
            $this->addEnvironmentChanges($changes);
        }
        if ($this instanceof HasPostInstall) {
            $this->postInstall();
        }
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    public function publishAssets($options)
    {
        foreach ($options as $option) {
            $this->runProcess->run($this->artisan->getPublishCommand($option));
        }
    }

    public function addMenuItems($items)
    {
        foreach ($items as $item) {
            $this->sidebarUpdater->addItem($item);
        }
    }

    public function addEnvironmentChanges($changes)
    {
        $this->environment->addChanges($changes);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function prePublish()
    {
    }

}