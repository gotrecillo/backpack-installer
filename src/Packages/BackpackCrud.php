<?php

namespace Gotrecillo\BackpackInstaller\Packages;

use Gotrecillo\BackpackInstaller\Interfaces\HasPostInstall;
use Gotrecillo\BackpackInstaller\Interfaces\HasMenuItems;
use Gotrecillo\BackpackInstaller\Interfaces\HasPublishableAssets;

class BackpackCrud extends Package implements HasPostInstall, HasPublishableAssets, HasMenuItems
{

    public function setup()
    {
        $this->setSlug('backpack/crud');
        $this->setName('Backpack Crud');
    }

    public function postInstall()
    {
        $this->mountManager->createDir('project://public/uploads');
        $this->mountManager->delete('project://config/filesystems.php');
        $this->mountManager->copy('resources://BackpackCrud/filesystems.php', 'project://config/filesystems.php');
        $elfinderCommand = $this->artisan->getElfinderPublishCommand();
        $this->runProcess->run($elfinderCommand);
    }

    public function getPublishableOptions()
    {
        return [
            '--provider="Backpack\CRUD\CrudServiceProvider"',
        ];
    }

    public function getMenuItems()
    {
        return [
            "<li><a href=\"{{ url(config('backpack.base.route_prefix', 'admin') . '/elfinder') }}\"><i class=\"fa fa-files-o\"></i> <span>File manager</span></a></li>",
        ];
    }
}