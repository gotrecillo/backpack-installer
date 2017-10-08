<?php

namespace Gotrecillo\BackpackInstaller\Packages;

class BackpackGenerators extends Package
{

    public function setup()
    {
        $this->setSlug('backpack/generators --dev');
        $this->setName('Backpack Generators');
    }
}