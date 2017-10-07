<?php

namespace Backpack\Install\Services;

use Backpack\Install\App;
use League\Flysystem\MountManager;

class Customizer
{
    protected $app;
    /**
     * @var MountManager
     */
    protected $mountManager;
    /**
     * @var SideBarUpdater
     */
    protected $sideBarUpdater;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->mountManager = $app->make('mountManager');
        $this->sideBarUpdater = $app->make('sideBarUpdater');
    }

    public function customize($options)
    {
        $this->updateBaseConfig($options);

        if($options['deleteAuthControllers']){
            $this->deleteAuthControllers();
        }

        $this->sideBarUpdater->closeSidebar();
    }

    private function updateBaseConfig($options)
    {
        $stub = $this->mountManager->read('resources://stubs/baseConfig.stub');

        $baseConfig = strtr($stub, [
            "{{:name:}}" => ucfirst($options['name']),
            "{{:initial:}}" => ucfirst(substr($options['name'], 0, 1)),
            "{{:developer:}}" => $options['developer'],
            "{{:website:}}" => $options['website'],
        ]);

        $this->mountManager->update('project://config/backpack/base.php', $baseConfig);
    }

    private function deleteAuthControllers()
    {
        $this->mountManager->deleteDir('project://app/Http/Controllers/Auth');
    }
}