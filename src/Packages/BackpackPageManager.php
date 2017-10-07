<?php

namespace Backpack\Install\Packages;

use Backpack\Install\Interfaces\HasPostInstall;
use Backpack\Install\Interfaces\HasMenuItems;
use Backpack\Install\Interfaces\HasPublishableAssets;

class BackpackPageManager extends Package implements HasPublishableAssets, HasMenuItems
{

    public function setup()
    {
        $this->setSlug('backpack/pagemanager');
        $this->setName('Backpack Crud');
    }

    public function getPublishableOptions()
    {
        return [
            '--provider="Backpack\PageManager\PageManagerServiceProvider"',
        ];
    }

    public function getMenuItems()
    {
        return [
            "<li><a href=\"{{ url(config('backpack.base.route_prefix', 'admin') . '/page') }}\"><i class=\"fa fa-file-o\"></i> <span>Pages</span></a></li>",
        ];
    }
}