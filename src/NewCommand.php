<?php

namespace Backpack\Install;

use Backpack\Install\Config\Config;
use Backpack\Install\Exceptions\FilesystemException;
use Backpack\Install\Interfaces\Configurable;
use Backpack\Install\Services\Composer;
use Backpack\Install\Services\Customizer;
use Backpack\Install\Services\PackageInstaller;
use Backpack\Install\Services\RunProcess;
use League\CLImate\CLImate;
use League\Flysystem\MountManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command
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

    public function configure()
    {
        $this->setName('new')
            ->setDescription('Create a backpack application')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addOption('prompt', 'p', InputOption::VALUE_NONE);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $prompt = $input->getOption('prompt');

        $this->setupFolder($name);
        $this->setupCommand();

        $this->cli->info("Let's configure the application:");
        $options = $prompt ? $this->getFormResponses() : $this->getDefaultOptions();

        $this->buildProject($options['packages']);

        $this->customizer->customize(array_merge(compact('name'), $options));

        $this->cli->info('Application ready');
    }

    private function setupFolder($name)
    {
        if (is_dir($name)) {
            throw new FilesystemException(sprintf('Failed to make application, directory %s already exists', $name));
        }

        if (!mkdir($name)) {
            throw new FilesystemException('Error making the directory for: ' . $name);
        };

        if (!chdir($name)) {
            throw new FilesystemException('Error changing the directory to: ' . $name);
        }
    }

    private function setupCommand()
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
    }

    private function getFormResponses()
    {
        $developer = $this
            ->cli
            ->lightYellow()
            ->input('Developer or company name:')
            ->defaultTo(getenv('DEVELOPER_NAME'))
            ->prompt();

        $website = $this
            ->cli
            ->lightYellow()
            ->input('Developer or company website:')
            ->defaultTo(getenv('DEVELOPER_LINK'))
            ->prompt();

        $deleteAuthControllers = $this
            ->cli
            ->lightYellow()
            ->confirm('Do you want to remove the laravel default auth controllers?:')
            ->confirmed();
        $packages = $this->promptPackages();

        return compact('packages', 'developer', 'website', 'deleteAuthControllers');
    }

    private function promptPackages()
    {
        $allPackages = $this->config->getPackages();

        return $this->cli
            ->lightYellow()
            ->checkboxes('What packages do you want to install', $allPackages)
            ->prompt();
    }

    private function getDefaultOptions()
    {
        $packages = $this->promptPackages();
        $developer = getenv('DEVELOPER_NAME');
        $website = getenv('DEVELOPER_LINK');
        $deleteAuthControllers = getenv('DELETE_AUTH_CONTROLLERS');

        return compact('packages', 'developer', 'website', 'deleteAuthControllers');
    }

    private function buildProject($packages)
    {
        $packageInstances = [];

        $defaultPackages = $this->config->getDefaultPackages();

        foreach ($defaultPackages as $package => $label) {
            $packageInstances[] = $this->app->make($package);
        }
        foreach ($packages as $package) {
            $packageInstances[] = $this->app->make($package);
        }

        foreach ($packageInstances as $package) {
            if($package instanceof Configurable)
            $package->configure();
        }

        $this->cli->info('Creating application....');
        $command = $this->composer->getCreateProjectCommand();
        $this->runProcess->run($command);
        $this->runProcess->run('chmod -R o+w storage bootstrap/cache');


        foreach ($packageInstances as $package) {
            $this->packageInstaller->install($package);
        }
    }

}