<?php

namespace Gotrecillo\BackpackInstaller\Services;

use League\Flysystem\MountManager;

class Provider
{
    /**
     * @var FileModifier
     */
    private $fileModifier;
    /**
     * @var MountManager
     */
    private $mountManager;

    public function __construct(FileModifier $fileModifier, MountManager $mountManager)
    {

        $this->fileModifier = $fileModifier;
        $this->mountManager = $mountManager;
    }

    public function add($provider)
    {
        $providerToken = $this->mountManager->read('resources://provider-token.stub');
        $this->fileModifier->addAfterHook('project://config/app.php', $providerToken, $provider, 8);
    }
}