<?php

namespace Gotrecillo\BackpackInstaller;

use Gotrecillo\BackpackInstaller\Config\Config;
use Gotrecillo\BackpackInstaller\Exceptions\ClassDoesNotExist;
use Gotrecillo\BackpackInstaller\Services\Artisan;
use Gotrecillo\BackpackInstaller\Services\Composer;
use Gotrecillo\BackpackInstaller\Services\Customizer;
use Gotrecillo\BackpackInstaller\Services\Environment;
use Gotrecillo\BackpackInstaller\Services\FileModifier;
use Gotrecillo\BackpackInstaller\Services\PackageInstaller;
use Gotrecillo\BackpackInstaller\Services\Provider;
use Gotrecillo\BackpackInstaller\Services\RunProcess;
use Gotrecillo\BackpackInstaller\Services\SideBarUpdater;
use League\CLImate\CLImate;
use League\Container\Container;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use Symfony\Component\Console\Application;

class App extends Application
{
    public $container;
    private $config;

    public function __construct(Config $config, Container $container)
    {
        $this->container = $container;
        $this->config = $config;

        parent::__construct($config->getName(), $config->getVersion());
    }

    public function registerServices()
    {
        $this->registerApp();
        $this->registerConfig($this->config);
        $this->registerFilesystems();
        $this->registerCli();
        $this->registerUtils();
        $this->registerPackages($this->config->getAllPackages());
    }

    private function registerApp()
    {
        $this->container->add('app', $this);
    }

    private function registerConfig($config)
    {
        $this->container->add('config', $config);
    }

    private function registerFilesystems()
    {
        $this->container
            ->add('projectAdapter', Local::class)
            ->withArgument(getcwd());

        $this->container
            ->add('projectFilesystem', Filesystem::class)
            ->withArgument('projectAdapter');

        $this->container
            ->add('resourcesAdapter', Local::class)
            ->withArgument(realpath(__DIR__ . '/../resources/'));

        $this->container
            ->add('resourcesFilesystem', Filesystem::class)
            ->withArgument('resourcesAdapter');

        $managers = [
            'resources' => $this->container->get('resourcesFilesystem'),
            'project' => $this->container->get('projectFilesystem'),
        ];

        $this->container
            ->add('mountManager', MountManager::class)
            ->withArgument($managers);
    }

    private function registerCli()
    {
        $this->container->add('cli', CLImate::class);
    }

    private function registerUtils()
    {
        $this->container
            ->add('artisan', Artisan::class);

        $this->container
            ->add('composer', Composer::class)
            ->withArgument('projectFilesystem');

        $this->container
            ->add('runProcess', RunProcess::class)
            ->withArgument('cli');

        $this->container
            ->add('packageInstaller', PackageInstaller::class)
            ->withArgument('cli');

        $this->container
            ->add('customizer', Customizer::class)
            ->withArgument('app');

        $this->container
            ->add('fileModifier', FileModifier::class)
            ->withArgument('mountManager');

        $this->container
            ->add('sideBarUpdater', SideBarUpdater::class)
            ->withArgument('fileModifier');

        $this->container
            ->add('environment', Environment::class)
            ->withArgument('fileModifier');

        $this->container
            ->add('provider', Provider::class)
            ->withArgument('fileModifier')
            ->withArgument('mountManager');

    }

    private function registerPackages($packages)
    {
        foreach ($packages as $package => $label) {
            $className = $this->getClassName('Gotrecillo\\BackpackInstaller\\Packages\\', $package);

            $this->container
                ->add($package, $className)
                ->withArgument('app');
        }
    }

    private function getClassName($namespace, $name)
    {
        $className = $namespace . $name;

        if (!class_exists($className)) {
            throw new ClassDoesNotExist("Could not find class: " . $className);
        }

        return $className;
    }

    public function make($abstract)
    {
        return $this->container->get($abstract);
    }

}