<?php

namespace Gotrecillo\BackpackInstaller\Packages;

use Gotrecillo\BackpackInstaller\Interfaces\HasPostInstall;
use Gotrecillo\BackpackInstaller\Interfaces\HasMenuItems;
use Gotrecillo\BackpackInstaller\Interfaces\HasPublishableAssets;

class BackpackBackupManager extends Package implements HasPublishableAssets, HasMenuItems, HasPostInstall
{

    public function setup()
    {
        $this->setSlug('backpack/backupmanager');
        $this->setName('Backpack BackupManager');
    }

    public function postInstall()
    {
        $backupsFile = $this->mountManager->read('resources://BackpackBackupManager/backup-file.stub');
        $this->fileModifier->addBeforeHook('project://config/filesystems.php', "'uploads' =>", $backupsFile, 8);
    }

    public function getPublishableOptions()
    {
        return [
            '--provider="Backpack\BackupManager\BackupManagerServiceProvider""',
        ];
    }


    public function getMenuItems()
    {
        return [
            "<li><a href=\"{{ url(config('backpack.base.route_prefix', 'admin').'/backup') }}\"><i class=\"fa fa-hdd-o\"></i> <span>Backups</span></a></li>",
        ];
    }
}
