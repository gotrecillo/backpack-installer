<?php

namespace Backpack\Install\Services;

use League\Flysystem\Filesystem;

class Composer
{
    private $projectFilesystem;

    public function __construct(Filesystem $projectFilesystem)
    {
        $this->projectFilesystem = $projectFilesystem;
    }

    public function getCreateProjectCommand()
    {
        return 'composer create-project --prefer-dist --ansi laravel/laravel .';
    }

    public function getAddPackageCommand($slug)
    {
        return 'composer require '. $slug;
    }
}