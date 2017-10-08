<?php

namespace Gotrecillo\BackpackInstaller\Packages;

use Gotrecillo\BackpackInstaller\Interfaces\Configurable;
use Gotrecillo\BackpackInstaller\Interfaces\HasEnvironmentVars;
use Gotrecillo\BackpackInstaller\Interfaces\HasMenuItems;
use Gotrecillo\BackpackInstaller\Interfaces\HasPostInstall;

class BackpackLogManager extends Package implements HasMenuItems, HasPostInstall, HasEnvironmentVars, Configurable
{
    protected $daily;

    public function setup()
    {
        $this->setSlug('backpack/logmanager');
        $this->setName('Backpack LogManager');
    }

    public function postInstall()
    {
        $storageFile = $this->mountManager->read('resources://BackpackLogManager/storage-file.stub');
        $this->fileModifier->addBeforeHook('project://config/filesystems.php', "'uploads' =>", $storageFile, 8);
    }


    public function getMenuItems()
    {
        return [
            "<li><a href=\"{{ url(config('backpack.base.route_prefix', 'admin') . '/log') }}\"><i class=\"fa fa-terminal\"></i> <span>Logs</span></a></li>",
        ];
    }

    public function getEnvironmentChanges()
    {
        return $this->daily ? ['APP_LOG' => 'daily'] : [];
    }

    public function configure()
    {
        $this->cli->info('Configure Backpack LogManager...');
        $this->daily = $this
            ->cli
            ->lightYellow()
            ->confirm('Do you want to store the logs by day instead of having only one log file')
            ->confirmed();
    }
}
