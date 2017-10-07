<?php

namespace Backpack\Install\Packages;

class BackpackGenerators extends Package
{

    public function setup()
    {
        $this->setSlug('backpack/generators --dev');
        $this->setName('Backpack Generators');
    }
}