<?php

namespace Gotrecillo\BackpackInstaller\Packages;

use Gotrecillo\BackpackInstaller\Interfaces\HasMenuItems;
use Gotrecillo\BackpackInstaller\Interfaces\HasPostInstall;
use Gotrecillo\BackpackInstaller\Interfaces\HasPublishableAssets;

class BackpackMenusCrud extends Package implements HasPublishableAssets, HasMenuItems
{

    public function setup()
    {
        $this->setSlug('backpack/menuCRUD');
        $this->setName('Backpack MenuCrud');
    }


    public function prePublish()
    {
        $provider = 'Backpack\MenuCRUD\MenuCRUDServiceProvider::class,';
        $this->provider->add($provider);
    }

    public function getPublishableOptions()
    {
        return [
            '--provider="Backpack\MenuCRUD\MenuCRUDServiceProvider"',
        ];
    }

    public function getMenuItems()
    {
        return [
            "<li><a href=\"{{ url(config('backpack.base.route_prefix', 'admin') . '/menu-item') }}\"><i class=\"fa fa-list\"></i> <span>Menu</span></a></li>",
        ];
    }
}