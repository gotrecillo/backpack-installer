<?php

namespace Gotrecillo\BackpackInstaller\Packages;

use Gotrecillo\BackpackInstaller\Interfaces\HasMenuItems;
use Gotrecillo\BackpackInstaller\Interfaces\HasPostInstall;
use Gotrecillo\BackpackInstaller\Interfaces\HasPublishableAssets;

class BackpackNewsCrud extends Package implements HasPublishableAssets, HasMenuItems, HasPostInstall
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

    public function postInstall()
    {
        $this->runProcess->run($this->artisan->migrate());
    }
}
