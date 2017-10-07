<?php

namespace Backpack\Install\Packages;

use Backpack\Install\Interfaces\HasMenuItems;
use Backpack\Install\Interfaces\HasPostInstall;
use Backpack\Install\Interfaces\HasPublishableAssets;

class BackpackNewsCrud extends Package implements HasPublishableAssets, HasMenuItems
{

    public function setup()
    {
        $this->setSlug('backpack/newscrud');
        $this->setName('Backpack NewsCrud');
    }


    public function prePublish()
    {
        $provider = 'Backpack\NewsCRUD\NewsCRUDServiceProvider::class,';
        $this->provider->add($provider);
    }

    public function getPublishableOptions()
    {
        return [
            '--provider="Backpack\NewsCRUD\NewsCRUDServiceProvider"',
        ];
    }

    public function getMenuItems()
    {
        $menuItem = $this->mountManager->read('resources://BackpackNewsCrud/menu-item.stub');

        return [
            $menuItem,
        ];
    }
}