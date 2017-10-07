<?php

namespace Backpack\Install\Packages;

use Backpack\Install\App;
use Backpack\Install\Interfaces\HasEnvironmentVars;
use Backpack\Install\Interfaces\HasMenuItems;
use Backpack\Install\Interfaces\HasPostInstall;
use Backpack\Install\Interfaces\HasPublishableAssets;
use Backpack\Install\Interfaces\Package as PackageInterface;
use Backpack\Install\Services\Artisan;
use Backpack\Install\Services\Composer;
use Backpack\Install\Services\Environment;
use Backpack\Install\Services\FileModifier;
use Backpack\Install\Services\PackageInstaller;
use Backpack\Install\Services\Provider;
use Backpack\Install\Services\RunProcess;
use Backpack\Install\Services\SideBarUpdater;
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