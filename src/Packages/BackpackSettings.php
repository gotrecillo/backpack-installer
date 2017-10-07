<?php

namespace Backpack\Install\Packages;

use Backpack\Install\Interfaces\HasMenuItems;
use Backpack\Install\Interfaces\HasPublishableAssets;

class BackpackSettings extends Package implements HasPublishableAssets, HasMenuItems
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
}