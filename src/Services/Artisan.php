<?php

namespace Backpack\Install\Services;

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
}