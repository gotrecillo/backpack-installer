<?php

namespace Gotrecillo\BackpackInstaller\Packages;

use Gotrecillo\BackpackInstaller\Interfaces\HasMenuItems;
use Gotrecillo\BackpackInstaller\Interfaces\HasPostInstall;
use Gotrecillo\BackpackInstaller\Interfaces\HasPublishableAssets;

class BackpackLangFileManager extends Package implements HasPublishableAssets, HasMenuItems, HasPostInstall
{
    public function setup()
    {
        $this->setSlug('backpack/langfilemanager');
        $this->setName('Backpack LangFileManager');
    }

    public function getPublishableOptions()
    {
        return [
            '--provider="Backpack\LangFileManager\LangFileManagerServiceProvider"',
        ];
    }


    public function getMenuItems()
    {
        return [
            "<li><a href=\"{{ url(config('backpack.base.route_prefix', 'admin') . '/language') }}\"><i class=\"fa fa-flag-o\"></i> <span>Languages</span></a></li>",
            "<li><a href=\"{{ url(config('backpack.base.route_prefix', 'admin') . '/language/texts') }}\"><i class=\"fa fa-language\"></i> <span>Language Files</span></a></li>",
        ];
    }


    public function postInstall()
    {
        $this->runProcess->run($this->artisan->migratePath("vendor/backpack/langfilemanager/src/database/migrations"));
        $this->runProcess->run($this->artisan->seedClass('Backpack\LangFileManager\database\seeds\LanguageTableSeeder'));
    }
}
