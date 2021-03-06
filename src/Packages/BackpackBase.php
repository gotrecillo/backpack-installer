<?php

namespace Gotrecillo\BackpackInstaller\Packages;

use Gotrecillo\BackpackInstaller\Interfaces\HasPostInstall;
use Gotrecillo\BackpackInstaller\Interfaces\HasPublishableAssets;

class BackpackBase extends Package implements HasPostInstall, HasPublishableAssets
{

    public function setup()
    {
        $this->setSlug('backpack/base');
        $this->setName('Backpack Base');
    }

    public function postInstall()
    {
        $additionalPackages = ['BackpackGenerators', 'LaracastsGenerators'];
        $this->mountManager->delete('project://app/User.php');
        $this->mountManager->delete('project://resources/views/vendor/backpack/base/inc/sidebar.blade.php');
        $this->mountManager->copy('resources://BackpackBase/User.php', 'project://app/User.php');
        $this->mountManager->copy(
            'resources://BackpackBase/sidebar.blade.php',
            'project://resources/views/vendor/backpack/base/inc/sidebar.blade.php'
        );

        foreach ($additionalPackages as $package) {
            $this->packageInstaller->install($this->app->make($package));
        }

        $this->runProcess->run($this->artisan->migrateFresh());
    }

    public function getPublishableOptions()
    {
        return [
            '--provider="Backpack\Base\BaseServiceProvider"',
            '--provider="Prologue\Alerts\AlertsServiceProvider"',
        ];
    }
}