<?php

namespace Gotrecillo\BackpackInstaller\Packages;

use Gotrecillo\BackpackInstaller\Interfaces\HasMenuItems;
use Gotrecillo\BackpackInstaller\Interfaces\HasPostInstall;
use Gotrecillo\BackpackInstaller\Interfaces\HasPublishableAssets;

class BackpackSettings extends Package implements HasPublishableAssets, HasMenuItems, HasPostInstall
{

    public function setup()
    {
        $this->setSlug('backpack/settings');
        $this->setName('Backpack Settings');
    }

    public function getPublishableOptions()
    {
        return [
            '--provider="Backpack\Settings\SettingsServiceProvider"',
        ];
    }

    public function getMenuItems()
    {
        return [
            "<li><a href=\"{{ url(config('backpack.base.route_prefix', 'admin') . '/setting') }}\"><i class=\"fa fa-cog\"></i> <span>Settings</span></a></li>"
        ];
    }

    public function postInstall()
    {
        $this->runProcess->run($this->artisan->migrate());
        $this->runProcess->run($this->artisan->seedClass('Backpack\Settings\database\seeds\SettingsTableSeeder'));
    }
}
