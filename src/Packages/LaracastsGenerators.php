<?php

namespace Backpack\Install\Packages;

class LaracastsGenerators extends Package
{

    public function setup()
    {
        $this->setSlug('laracasts/generators:dev-master --dev');
        $this->setName('Laracasts Generators');
    }
}