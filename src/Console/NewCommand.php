<?php

namespace Gotrecillo\BackpackInstaller\Console;

use Gotrecillo\BackpackInstaller\Exceptions\FilesystemException;
use Gotrecillo\BackpackInstaller\Interfaces\Configurable;
use PDO;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends BaseCommand
{
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
        $database = $this->getDatabaseConfig();

        $this->buildProject($options['packages'], $database);

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

    private function getFormResponses()
    {
        $developer = $this
            ->cli
            ->lightYellow()
            ->input('Developer or company name:')
            ->defaultTo($this->config->getOption('developerName'))
            ->prompt();

        $website = $this
            ->cli
            ->lightYellow()
            ->input('Developer or company website:')
            ->defaultTo($this->config->getOption('developerLink'))
            ->prompt();

        $deleteAuthControllers = $this
            ->cli
            ->lightYellow()
            ->confirm('Do you want to remove the laravel default auth controllers?:')
            ->confirmed();

        $packages = $this->promptPackages();

        return compact('packages', 'developer', 'website', 'deleteAuthControllers', 'database');
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
        $developer = $this->config->getOption('developerName');
        $website = $this->config->getOption('developerLink');
        $deleteAuthControllers = $this->config->getOption('deleteAuthControllers');

        return compact('packages', 'developer', 'website', 'deleteAuthControllers');
    }

    private function getDatabaseConfig()
    {
        $user = $this->config->getOption('databaseUser');
        $password = $this->config->getOption('databasePassword');

        do {
            $database = $this
                ->cli
                ->lightYellow()
                ->input('What database will be used?')->prompt();
        } while (!$database);

        $connection = new PDO("mysql:host=localhost", $user, $password);

        $connection->exec("CREATE DATABASE `${database}`;");

        return compact('database', 'user', 'password');
    }

    private function buildProject($packages, $database)
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
            if ($package instanceof Configurable)
                $package->configure();
        }

        $this->cli->info('Creating application....');
        $command = $this->composer->getCreateProjectCommand();
        $this->runProcess->run($command);
        $this->runProcess->run('chmod -R o+w storage bootstrap/cache');
        $this->environment->updateKey('DB_DATABASE', $database['database']);
        $this->environment->updateKey('DB_USERNAME', $database['user']);
        $this->environment->updateKey('DB_PASSWORD', $database['password']);

        foreach ($packageInstances as $package) {
            $this->packageInstaller->install($package);
        }
    }

}