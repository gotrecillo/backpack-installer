<?php

namespace Gotrecillo\BackpackInstaller\Services;

class SideBarUpdater
{
    const FILE_LOCATION = 'project://resources/views/vendor/backpack/base/inc/sidebar.blade.php';
    const HOOK = "{{:sidebar-hook:}}";
    const PADDING = 16;
    private $fileModifier;

    public function __construct(FileModifier $fileModifier)
    {
        $this->fileModifier = $fileModifier;
    }

    public function addItem($item)
    {
        $this->fileModifier->addBeforeHook(static::FILE_LOCATION, static::HOOK, $item, static::PADDING);
    }

    public function closeSidebar()
    {
        $this->fileModifier->removeHook(static::FILE_LOCATION, static::HOOK);
    }

}