<?php

namespace Gotrecillo\BackpackInstaller\Packages;

use Gotrecillo\BackpackInstaller\Interfaces\HasMenuItems;
use Gotrecillo\BackpackInstaller\Interfaces\HasPostInstall;
use Gotrecillo\BackpackInstaller\Interfaces\HasPublishableAssets;

class BackpackPermissionManager extends Package implements HasPublishableAssets, HasMenuItems, HasPostInstall
{

    public function setup()
    {
        $this->setSlug('backpack/permissionmanager');
        $this->setName('Backpack PermissionManager');
    }


    public function prePublish()
    {
        $provider = 'Backpack\PermissionManager\PermissionManagerServiceProvider::class,';
        $this->provider->add($provider);
    }

    public function getPublishableOptions()
    {
        return [
            '--provider="Backpack\PermissionManager\PermissionManagerServiceProvider"',
        ];
    }

    public function getMenuItems()
    {
        $menuItem = $this->mountManager->read('resources://BackpackPermissionManager/menu-item.stub');

        return [
            $menuItem,
        ];
    }

    public function postInstall()
    {
        $userPath = 'project://app/User.php';
        $traitHook = $this->mountManager->read('resources://BackpackPermissionManager/user-trait-hook.stub');
        $traits = $this->mountManager->read('resources://BackpackPermissionManager/user-traits.stub');
        $useHook = $this->mountManager->read('resources://BackpackPermissionManager/user-use-hook.stub');
        $uses = $this->mountManager->read('resources://BackpackPermissionManager/user-use.stub');

        $this->fileModifier->addAfterHook($userPath, $traitHook, $traits, 4);
        $this->fileModifier->addAfterHook($userPath, $useHook, $uses);
    }
}