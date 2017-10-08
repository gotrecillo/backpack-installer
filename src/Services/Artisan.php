<?php

namespace Gotrecillo\BackpackInstaller\Services;

class Artisan
{
    public function getPublishCommand($options)
    {
        return 'php artisan vendor:publish ' . $options;
    }

    public function getElfinderPublishCommand()
    {
        return 'php artisan elfinder:publish';
    }

    public function migrateFresh()
    {
        return 'php artisan migrate:fresh';
    }

    public function migrate()
    {
        return 'php artisan migrate';
    }

    public function migratePath($path)
    {
        return 'php artisan migrate --path='.$path;
    }

    public function seedClass($class)
    {
        return "php artisan db:seed --class=\"${class}\"";
    }
}